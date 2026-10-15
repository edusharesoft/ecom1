<?php

namespace App\Support;

/**
 * Steadfast delivery_status / ট্র্যাকিং মেসেজ → অ্যাপ অর্ডার স্ট্যাটাস।
 * ৬ = Completed · ১১ = Cancelled
 */
class SteadfastWebhookStatus
{
    public const STATUS_COMPLETED = 6;

    public const STATUS_CANCELLED = 11;

    public static function normalize(?string $status): string
    {
        $s = strtolower(trim((string) $status));
        $s = str_replace([' ', '-'], '_', $s);

        return $s;
    }

    /**
     * Steadfast webhook `status` বা API `delivery_status` → order_status ID.
     */
    public static function toOrderStatusId(?string $status): ?int
    {
        return self::mapNormalized(self::normalize($status));
    }

    /**
     * tracking_message থেকে ডেলিভারি/বাতিল অনুমান (ঐচ্ছিক ফলব্যাক)।
     */
    public static function fromTrackingMessage(?string $message): ?int
    {
        $m = strtolower(trim((string) $message));
        if ($m === '') {
            return null;
        }

        if (preg_match('/\b(cancelled|canceled|cancel|returned|return|বাতিল|প্রত্যাবর্তন)\b/u', $m)) {
            return self::STATUS_CANCELLED;
        }

        if (preg_match('/\b(delivered|delivery|partial_delivered|ডেলিভার|সফলভাবে\s*ডেলিভার)\b/u', $m)) {
            return self::STATUS_COMPLETED;
        }

        return null;
    }

    public static function isCompleted(int $statusId): bool
    {
        return $statusId === self::STATUS_COMPLETED;
    }

    public static function isCancelled(int $statusId): bool
    {
        return $statusId === self::STATUS_CANCELLED;
    }

    private static function mapNormalized(string $s): ?int
    {
        if ($s === '') {
            return null;
        }

        $delivered = [
            'delivered',
            'partial_delivered',
            'delivered_approval_pending',
            'partial_delivered_approval_pending',
            'delivery_success',
            'successfully_delivered',
        ];

        $cancelled = [
            'cancelled',
            'canceled',
            'cancelled_approval_pending',
            'cancel',
            'returned',
            'return',
        ];

        if (in_array($s, $delivered, true)) {
            return self::STATUS_COMPLETED;
        }

        if (in_array($s, $cancelled, true)) {
            return self::STATUS_CANCELLED;
        }

        // Steadfast কখনও Title Case পাঠায়: "Delivered" → delivered
        if (str_contains($s, 'deliver') && ! str_contains($s, 'cancel')) {
            return self::STATUS_COMPLETED;
        }

        if (str_contains($s, 'cancel') || str_contains($s, 'return')) {
            return self::STATUS_CANCELLED;
        }

        if (in_array($s, ['pending', 'unknown', 'in_review', 'hold', 'in_transit', 'picked_up'], true)) {
            return null;
        }

        return null;
    }
}
