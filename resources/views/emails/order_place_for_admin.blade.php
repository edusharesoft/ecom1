<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>New Order #{{ $order->invoice_id }}</title>
</head>
<body style="margin:0;padding:0;background:#eef2f7;font-family:'Segoe UI',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#eef2f7;padding:40px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.09);">

  {{-- ══ TOP ACCENT BAR ══ --}}
  <tr><td style="background:#4f46e5;height:5px;font-size:0;">&nbsp;</td></tr>

  {{-- ══ HEADER ══ --}}
  <tr>
    <td style="padding:36px 48px 28px;border-bottom:1px solid #f0f0f5;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <p style="margin:0 0 4px;font-size:11px;font-weight:700;letter-spacing:1.5px;color:#6366f1;text-transform:uppercase;">Order Notification</p>
            <h1 style="margin:0;font-size:26px;font-weight:800;color:#1e1b4b;">New Order Received</h1>
          </td>
          <td align="right" valign="top">
            <div style="background:#f0f0ff;border:1px solid #c7d2fe;border-radius:8px;padding:10px 18px;display:inline-block;text-align:center;">
              <p style="margin:0;font-size:11px;color:#6366f1;font-weight:600;letter-spacing:1px;">ORDER</p>
              <p style="margin:2px 0 0;font-size:18px;font-weight:800;color:#1e1b4b;">#{{ $order->invoice_id }}</p>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ ALERT BANNER ══ --}}
  <tr>
    <td style="padding:0 48px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#faf5ff;border-left:4px solid #7c3aed;border-radius:0 8px 8px 0;margin:24px 0;">
        <tr>
          <td style="padding:14px 20px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td style="font-size:22px;padding-right:12px;">🛒</td>
                <td>
                  <p style="margin:0;font-size:14px;font-weight:700;color:#1e1b4b;">A customer just placed an order!</p>
                  <p style="margin:2px 0 0;font-size:12px;color:#6b7280;">{{ $order->created_at->format('l, d F Y — h:i A') }}</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ ORDER & CUSTOMER SUMMARY ══ --}}
  <tr>
    <td style="padding:0 48px 24px;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>

          {{-- Order Info --}}
          <td width="48%" valign="top" style="padding-right:8px;">
            <p style="margin:0 0 10px;font-size:11px;font-weight:700;letter-spacing:1px;color:#9ca3af;text-transform:uppercase;">Order Details</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:10px 14px;font-size:12px;color:#6b7280;">Payment Method</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:600;color:#1f2937;text-align:right;text-transform:uppercase;">{{ $order->payment->payment_method ?? 'N/A' }}</td>
              </tr>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:10px 14px;font-size:12px;color:#6b7280;">Payment Status</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:600;text-align:right;">
                  @if(($order->payment->payment_status ?? '') === 'paid')
                    <span style="color:#059669;">● Paid</span>
                  @else
                    <span style="color:#d97706;">● Pending</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td style="padding:10px 14px;font-size:12px;color:#6b7280;">Order Status</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:600;color:#7c3aed;text-align:right;">● New / Pending</td>
              </tr>
            </table>
          </td>

          <td width="4%"></td>

          {{-- Customer Info --}}
          <td width="48%" valign="top" style="padding-left:8px;">
            <p style="margin:0 0 10px;font-size:11px;font-weight:700;letter-spacing:1px;color:#9ca3af;text-transform:uppercase;">Customer Details</p>
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:10px 14px;font-size:12px;color:#6b7280;">Name</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:600;color:#1f2937;text-align:right;">{{ $shipping->name ?? ($customer->name ?? 'N/A') }}</td>
              </tr>
              <tr style="border-bottom:1px solid #e5e7eb;">
                <td style="padding:10px 14px;font-size:12px;color:#6b7280;">Phone</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:600;color:#1f2937;text-align:right;">{{ $shipping->phone ?? ($customer->phone ?? 'N/A') }}</td>
              </tr>
              <tr>
                <td style="padding:10px 14px;font-size:12px;color:#6b7280;">Area</td>
                <td style="padding:10px 14px;font-size:12px;font-weight:600;color:#1f2937;text-align:right;">{{ $shipping->area ?? 'N/A' }}</td>
              </tr>
            </table>
          </td>

        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ SHIPPING ADDRESS ══ --}}
  @if(!empty($shipping->address))
  <tr>
    <td style="padding:0 48px 24px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;">
        <tr>
          <td style="padding:12px 18px;">
            <p style="margin:0 0 2px;font-size:11px;font-weight:700;color:#92400e;letter-spacing:1px;text-transform:uppercase;">📍 Delivery Address</p>
            <p style="margin:0;font-size:13px;color:#1f2937;">{{ $shipping->address }}@if(!empty($shipping->area)), {{ $shipping->area }}@endif</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  @endif

  {{-- ══ PRODUCTS TABLE ══ --}}
  <tr>
    <td style="padding:0 48px 24px;">
      <p style="margin:0 0 10px;font-size:11px;font-weight:700;letter-spacing:1px;color:#9ca3af;text-transform:uppercase;">Order Items</p>
      <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
        <thead>
          <tr style="background:#4f46e5;">
            <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#fff;letter-spacing:0.5px;text-transform:uppercase;">Product</th>
            <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#fff;letter-spacing:0.5px;text-transform:uppercase;">Qty</th>
            <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#fff;letter-spacing:0.5px;text-transform:uppercase;">Price</th>
            <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#fff;letter-spacing:0.5px;text-transform:uppercase;">Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orderDetails as $item)
          <tr style="background:{{ $loop->even ? '#f9fafb' : '#ffffff' }};border-top:1px solid #e5e7eb;">
            <td style="padding:12px 16px;font-size:13px;color:#374151;font-weight:500;">{{ $item->product_name }}</td>
            <td style="padding:12px 16px;font-size:13px;color:#6b7280;text-align:center;">{{ $item->qty }}</td>
            <td style="padding:12px 16px;font-size:13px;color:#6b7280;text-align:right;">৳{{ number_format($item->sale_price, 0) }}</td>
            <td style="padding:12px 16px;font-size:13px;color:#1f2937;font-weight:600;text-align:right;">৳{{ number_format($item->qty * $item->sale_price, 0) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </td>
  </tr>

  {{-- ══ ORDER TOTALS ══ --}}
  <tr>
    <td style="padding:0 48px 32px;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td align="right">
            <table cellpadding="0" cellspacing="0" style="min-width:260px;">
              <tr>
                <td style="padding:6px 0;font-size:13px;color:#6b7280;padding-right:24px;">Subtotal</td>
                <td style="padding:6px 0;font-size:13px;color:#374151;text-align:right;">৳{{ number_format($order->amount - $order->shipping_charge - ($order->discount ?? 0), 0) }}</td>
              </tr>
              @if($order->shipping_charge > 0)
              <tr>
                <td style="padding:6px 0;font-size:13px;color:#6b7280;padding-right:24px;">Shipping Charge</td>
                <td style="padding:6px 0;font-size:13px;color:#374151;text-align:right;">৳{{ number_format($order->shipping_charge, 0) }}</td>
              </tr>
              @endif
              @if(($order->discount ?? 0) > 0)
              <tr>
                <td style="padding:6px 0;font-size:13px;color:#059669;padding-right:24px;">Discount</td>
                <td style="padding:6px 0;font-size:13px;color:#059669;text-align:right;">-৳{{ number_format($order->discount, 0) }}</td>
              </tr>
              @endif
              <tr>
                <td colspan="2" style="padding:8px 0;"><div style="border-top:2px dashed #e5e7eb;"></div></td>
              </tr>
              <tr style="background:#f0f0ff;border-radius:6px;">
                <td style="padding:10px 16px;font-size:15px;font-weight:800;color:#1e1b4b;">Grand Total</td>
                <td style="padding:10px 16px;font-size:18px;font-weight:800;color:#4f46e5;text-align:right;">৳{{ number_format($order->amount, 0) }}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  @if($order->note)
  {{-- ══ ORDER NOTE ══ --}}
  <tr>
    <td style="padding:0 48px 28px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;">
        <tr>
          <td style="padding:12px 18px;">
            <p style="margin:0 0 2px;font-size:11px;font-weight:700;color:#166534;letter-spacing:1px;text-transform:uppercase;">💬 Customer Note</p>
            <p style="margin:0;font-size:13px;color:#374151;">{{ $order->note }}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  @endif

  {{-- ══ CTA BUTTON ══ --}}
  <tr>
    <td style="padding:0 48px 40px;text-align:center;">
      <a href="{{ url('admin/order/all') }}"
         style="display:inline-block;background:#4f46e5;color:#ffffff;padding:14px 40px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:700;letter-spacing:0.5px;">
        View Order in Admin Panel →
      </a>
      <p style="margin:12px 0 0;font-size:12px;color:#9ca3af;">
        Or copy: <span style="color:#4f46e5;">{{ url('admin/order/all') }}</span>
      </p>
    </td>
  </tr>

  {{-- ══ FOOTER ══ --}}
  <tr>
    <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:20px 48px;">
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td>
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              <strong style="color:#6b7280;">{{ config('app.name') }}</strong> — Automated Order Notification
            </p>
          </td>
          <td align="right">
            <p style="margin:0;font-size:11px;color:#d1d5db;">{{ now()->format('d M Y') }}</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  {{-- ══ BOTTOM ACCENT BAR ══ --}}
  <tr><td style="background:#4f46e5;height:3px;font-size:0;">&nbsp;</td></tr>

</table>
</td></tr>
</table>

</body>
</html>
