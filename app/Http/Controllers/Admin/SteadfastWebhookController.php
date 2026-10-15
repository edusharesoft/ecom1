<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courierapi;
use App\Models\Order;
use App\Services\CourierWebhookOrderService;
use App\Support\SteadfastWebhookStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SteadfastWebhookController extends Controller
{
    public function __construct(
        private readonly CourierWebhookOrderService $webhookOrders
    ) {}

    /**
     * Steadfast webhook — delivery_status | tracking_update
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            Log::info('Steadfast Webhook Received', [
                'payload' => $request->all(),
                'ip'      => $request->ip(),
            ]);

            if (! $this->authorizeWebhook($request)) {
                return $this->jsonError('Unauthorized', 401);
            }

            $type = strtolower(trim((string) $request->input('notification_type', '')));

            return match ($type) {
                'delivery_status' => $this->handleDeliveryStatus($request),
                'tracking_update' => $this->handleTrackingUpdate($request),
                default           => $this->jsonError('Unknown or missing notification_type', 400),
            };
        } catch (\Throwable $e) {
            Log::error('Steadfast Webhook Error', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return $this->jsonError('Internal server error', 500);
        }
    }

    private function handleDeliveryStatus(Request $request): JsonResponse
    {
        $consignmentId = $request->input('consignment_id');
        $invoice         = $request->input('invoice');
        $status          = $request->input('status');

        if ($consignmentId === null && $invoice === null) {
            return $this->jsonError('consignment_id or invoice is required', 400);
        }

        if ($status === null || trim((string) $status) === '') {
            return $this->jsonError('status is required', 400);
        }

        $order = $this->findOrder($consignmentId, $invoice);

        if (! $order) {
            Log::warning('Steadfast Webhook: Order not found', [
                'consignment_id' => $consignmentId,
                'invoice'          => $invoice,
            ]);

            return $this->jsonError('Invalid consignment ID or invoice.', 404);
        }

        if ($consignmentId !== null && empty($order->consignment_id)) {
            $order->consignment_id = (string) $consignmentId;
        }

        if ($consignmentId !== null && empty($order->courier_tracking_id)) {
            $order->courier_tracking_id = (string) $consignmentId;
        }

        $order->courier_type = $order->courier_type ?: 'steadfast';
        $order->save();

        $trackingMessage = trim((string) $request->input('tracking_message', ''));
        if ($trackingMessage !== '') {
            $this->webhookOrders->appendCourierNote($order, $trackingMessage);
            $order->refresh();
        }

        $newStatusId = SteadfastWebhookStatus::toOrderStatusId((string) $status)
            ?? SteadfastWebhookStatus::fromTrackingMessage($trackingMessage);

        $this->applyOrderStatusFromSteadfast($order, $newStatusId, (string) $status, [
            'cod_amount'      => $request->input('cod_amount'),
            'delivery_charge' => $request->input('delivery_charge'),
        ]);

        return $this->jsonSuccess();
    }

    private function handleTrackingUpdate(Request $request): JsonResponse
    {
        $consignmentId = $request->input('consignment_id');
        $invoice       = $request->input('invoice');
        $message       = trim((string) $request->input('tracking_message', ''));

        if ($consignmentId === null && $invoice === null) {
            return $this->jsonError('consignment_id or invoice is required', 400);
        }

        if ($message === '') {
            return $this->jsonError('tracking_message is required', 400);
        }

        $order = $this->findOrder($consignmentId, $invoice);

        if (! $order) {
            return $this->jsonError('Invalid consignment ID or invoice.', 404);
        }

        if ($consignmentId !== null && empty($order->consignment_id)) {
            $order->consignment_id = (string) $consignmentId;
            $order->save();
        }

        $this->webhookOrders->appendCourierNote($order, $message);
        $order->refresh();

        $inferredStatus = SteadfastWebhookStatus::fromTrackingMessage($message);
        if ($inferredStatus !== null) {
            $this->applyOrderStatusFromSteadfast($order, $inferredStatus, 'tracking:'.$message);
        } else {
            Log::info('Steadfast Webhook: tracking_update (note only)', [
                'order_id'   => $order->id,
                'invoice_id' => $order->invoice_id,
                'message'    => $message,
            ]);
        }

        return $this->jsonSuccess();
    }

    private function applyOrderStatusFromSteadfast(Order $order, ?int $newStatusId, string $steadfastLabel, array $extra = []): void
    {
        if ($newStatusId === null) {
            Log::info('Steadfast Webhook: no order status map', [
                'order_id' => $order->id,
                'label'    => $steadfastLabel,
            ]);

            return;
        }

        $changed = $this->webhookOrders->applyStatusChange($order, $newStatusId, 'Steadfast');

        $statusName = SteadfastWebhookStatus::isCompleted($newStatusId) ? 'Completed'
            : (SteadfastWebhookStatus::isCancelled($newStatusId) ? 'Cancelled' : (string) $newStatusId);

        Log::info('Steadfast Webhook: order status '.($changed ? 'updated' : 'unchanged'), array_merge([
            'order_id'         => $order->id,
            'invoice_id'       => $order->invoice_id,
            'steadfast_status' => $steadfastLabel,
            'order_status_id'  => $newStatusId,
            'order_status'     => $statusName,
            'status_changed'   => $changed,
        ], $extra));
    }

    private function findOrder(mixed $consignmentId, mixed $invoice): ?Order
    {
        if ($consignmentId !== null && $consignmentId !== '') {
            $cid = trim((string) $consignmentId);

            $byCourier = Order::query()
                ->where(function ($q) use ($cid) {
                    $q->where('consignment_id', $cid)
                        ->orWhere('courier_tracking_id', $cid)
                        ->orWhere('courier_tracking_code', $cid);
                })
                ->orderByDesc('id')
                ->first();

            if ($byCourier) {
                return $byCourier;
            }
        }

        if ($invoice !== null && trim((string) $invoice) !== '') {
            $inv = trim((string) $invoice);

            return Order::query()
                ->where(function ($q) use ($inv) {
                    $q->where('invoice_id', $inv);
                    if (ctype_digit($inv)) {
                        $q->orWhere('id', (int) $inv);
                    }
                })
                ->orderByDesc('id')
                ->first();
        }

        return null;
    }

    private function authorizeWebhook(Request $request): bool
    {
        $cfg = Courierapi::where('type', 'steadfast')->where('status', 1)->first()
            ?? Courierapi::where('type', 'steadfast')->first();

        if (! $cfg || empty(trim((string) $cfg->token))) {
            return true;
        }

        $expected = trim((string) $cfg->token);
        $expected = preg_replace('/^Bearer\s+/i', '', $expected);

        $provided = $request->bearerToken();
        if ($provided === null) {
            $header = $request->header('Authorization', '');
            if (preg_match('/^Bearer\s+(.+)$/i', $header, $m)) {
                $provided = trim($m[1]);
            }
        }

        return $provided !== null && hash_equals($expected, $provided);
    }

    private function jsonSuccess(): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Webhook received successfully.',
        ], 200);
    }

    private function jsonError(string $message, int $code): JsonResponse
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
        ], $code);
    }
}
