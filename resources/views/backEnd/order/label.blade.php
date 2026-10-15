<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shipping Labels</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        @page { size: A4 portrait; margin: 6mm 6mm; }

        body {
            background: #c8c8c8;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        .no-print {
            text-align: center; padding: 10px;
            background: #1a1a1a; position: sticky; top: 0; z-index: 99;
        }
        .no-print button {
            padding: 8px 32px; background: #28a745; color: #fff;
            border: none; cursor: pointer; font-size: 14px; border-radius: 4px; font-weight: 600;
        }

        /* A4 page: 2 col × 4 row = 8 labels */
        .a4-page {
            background: #fff;
            width: 794px; min-height: 1120px;
            margin: 18px auto; padding: 5px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(4, 1fr);
            gap: 5px;
            border: 1px solid #999;
            box-shadow: 0 2px 12px rgba(0,0,0,.2);
        }

        /* Single label */
        .label {
            border: 1.5px solid #222;
            display: flex; flex-direction: column;
            overflow: hidden; font-size: 11px; position: relative;
        }
        .label.empty {
            border: 1px dashed #ccc;
            background: repeating-linear-gradient(45deg,#f9f9f9,#f9f9f9 5px,#f0f0f0 5px,#f0f0f0 10px);
        }

        /* Top bar — FROM + Invoice */
        .lbl-top {
            background: #1a1a1a; color: #fff;
            padding: 5px 8px;
            display: flex; justify-content: space-between; align-items: center;
            flex-shrink: 0;
        }
        .from-tag  { font-size:8px; text-transform:uppercase; letter-spacing:1px; color:#aaa; line-height:1; }
        .from-name { font-size:12px; font-weight:700; letter-spacing:.5px; }
        .from-cont { font-size:9px; color:#ccc; margin-top:1px; }
        .inv-block { text-align:right; }
        .inv-tag   { font-size:8px; color:#aaa; text-transform:uppercase; letter-spacing:1px; }
        .inv-num   { font-size:13px; font-weight:700; color:#fff; }
        .inv-date  { font-size:8px; color:#bbb; margin-top:1px; }

        /* TO section */
        .lbl-to { padding:7px 8px 5px; flex:1; border-bottom:1px dashed #bbb; }
        .to-tag  { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:#888; margin-bottom:3px; }
        .to-name { font-size:17px; font-weight:800; color:#000; line-height:1.1; }
        .to-phone{ font-size:13px; font-weight:700; color:#111; margin-top:3px; }
        .to-addr { font-size:10.5px; color:#333; margin-top:3px; line-height:1.4; }

        /* Products */
        .lbl-prods {
            padding: 4px 8px; border-bottom: 1px dashed #bbb;
            flex-shrink: 0;
        }
        .prod-row  { display:flex; align-items:center; gap:5px; margin-bottom:3px; }
        .prod-row:last-child { margin-bottom:0; }
        .prod-img  { width:36px; height:36px; object-fit:cover; border:1px solid #ddd; border-radius:2px; flex-shrink:0; background:#f5f5f5; }
        .prod-ph   { width:36px; height:36px; border:1px solid #ddd; border-radius:2px; flex-shrink:0; background:#f0f0f0; display:flex; align-items:center; justify-content:center; font-size:14px; }
        .prod-info { flex:1; font-size:9.5px; line-height:1.4; }
        .prod-name { font-weight:700; font-size:10px; }
        .prod-meta { display:flex; gap:4px; flex-wrap:wrap; margin-top:2px; }
        .badge-sz  { background:#e8f0fe; border:1px solid #b3c6f7; color:#1a56db; font-size:8.5px; padding:0 4px; border-radius:2px; }
        .badge-cl  { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; font-size:8.5px; padding:0 4px; border-radius:2px; }
        .badge-qty { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; font-size:8.5px; padding:0 4px; border-radius:2px; font-weight:700; }

        /* Bottom bar */
        .lbl-bottom { display:flex; align-items:stretch; flex-shrink:0; }
        .lbl-courier { flex:1; padding:5px 8px; border-right:1px solid #eee; font-size:9px; }
        .c-tag  { font-size:7.5px; text-transform:uppercase; letter-spacing:1px; color:#999; margin-bottom:2px; }
        .c-name { font-size:11px; font-weight:700; }
        .c-name.steadfast { color:#1a56db; }
        .c-name.pathao    { color:#0891b2; }
        .c-name.redx      { color:#d97706; }
        .c-name.none      { color:#bbb; }
        .c-id   { font-size:8.5px; color:#555; margin-top:1px; word-break:break-all; }
        .lbl-amount { width:90px; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:5px 6px; background:#f5f5f5; }
        .a-tag  { font-size:7.5px; text-transform:uppercase; letter-spacing:1px; color:#888; }
        .a-val  { font-size:14px; font-weight:800; color:#000; line-height:1.1; }
        .a-qty  { font-size:8.5px; color:#777; margin-top:1px; }

        /* COD ribbon */
        .cod-ribbon {
            position:absolute; top:0; right:0;
            background:#dc2626; color:#fff;
            font-size:7.5px; font-weight:700;
            padding:2px 8px 2px 10px;
            letter-spacing:1px;
            clip-path:polygon(8px 0%,100% 0%,100% 100%,0% 100%);
        }

        /* Print */
        @media print {
            body { background:#fff !important; }
            .no-print { display:none !important; }
            .a4-page {
                width:100% !important; min-height:unset !important;
                height:calc(297mm - 12mm) !important;
                margin:0 !important; padding:0 !important;
                border:none !important; box-shadow:none !important; gap:3px !important;
                page-break-after:always; break-after:page;
            }
            .a4-page:last-child { page-break-after:avoid; break-after:avoid; }
            .label { border:1px solid #333 !important; }
            .prod-img, .prod-ph, .badge-sz, .badge-cl, .badge-qty,
            .lbl-top, .cod-ribbon, .lbl-amount {
                -webkit-print-color-adjust:exact;
                print-color-adjust:exact;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">🖨 &nbsp;Print Labels</button>
</div>

@foreach($orders->chunk(8) as $chunk)
<div class="a4-page">

    @foreach($chunk as $order)
    @php
        $totalQty  = $order->orderdetails->sum('qty');
        $payMethod = strtolower($order->payment_gateway ?? ($order->payment ? $order->payment->payment_method : ''));
        $isCOD     = !in_array($payMethod, ['bkash','nagad','rocket','card','online','paid']);
        $trkId     = $order->courier_tracking_id ?? $order->consignment_id ?? null;
        $courier   = $order->courier_type ?? ($trkId ? 'steadfast' : null);
        $cClass    = $courier ? strtolower($courier) : 'none';
    @endphp

    <div class="label">
        @if($isCOD)<div class="cod-ribbon">COD</div>@endif

        {{-- TOP: FROM + Invoice --}}
        <div class="lbl-top">
            <div>
                <div class="from-tag">From</div>
                <div class="from-name">{{ $generalsetting->name }}</div>
                <div class="from-cont">
                    {{ $contact->phone ?? '' }}
                    @if($contact->address) &middot; {{ Str::limit($contact->address,35) }}@endif
                </div>
            </div>
            <div class="inv-block">
                <div class="inv-tag">Invoice</div>
                <div class="inv-num">#{{ $order->invoice_id }}</div>
                <div class="inv-date">{{ $order->created_at->format('d M Y') }}</div>
            </div>
        </div>

        {{-- TO: Recipient --}}
        <div class="lbl-to">
            <div class="to-tag">&#9658; Ship To</div>
            @if($order->shipping)
                <div class="to-name">{{ $order->shipping->name ?? '—' }}</div>
                <div class="to-phone">&#128222; {{ $order->shipping->phone ?? '' }}</div>
                <div class="to-addr">
                    {{ $order->shipping->address ?? '' }}
                    @if($order->shipping->area), {{ $order->shipping->area }}@endif
                </div>
            @else
                <div class="to-name">— No Shipping Info —</div>
            @endif
        </div>

        {{-- Products with image, size, color --}}
        <div class="lbl-prods">
            @foreach($order->orderdetails as $item)
            @php
                $imgPath = $item->image ? $item->image->image : null;
                $sz = $item->size ? ($item->size->sizeName ?? null) : null;
                if (!$sz && $item->product_size) {
                    $sm = \App\Models\Size::find($item->product_size);
                    $sz = $sm ? ($sm->sizeName ?? null) : (is_numeric($item->product_size) ? null : $item->product_size);
                }
                $cl = $item->color ? ($item->color->colorName ?? null) : null;
                if (!$cl && $item->product_color && !is_numeric($item->product_color)) { $cl = $item->product_color; }
            @endphp
            <div class="prod-row">
                @if($imgPath)
                    <img class="prod-img" src="{{ asset($imgPath) }}" alt=""
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="prod-ph" style="display:none;">&#128230;</div>
                @else
                    <div class="prod-ph">&#128230;</div>
                @endif
                <div class="prod-info">
                    <div class="prod-name">{{ Str::limit($item->product_name, 38) }}</div>
                    <div class="prod-meta">
                        @if($sz)<span class="badge-sz">Sz: {{ $sz }}</span>@endif
                        @if($cl)<span class="badge-cl">{{ $cl }}</span>@endif
                        <span class="badge-qty">&times; {{ $item->qty }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Bottom: Courier + Amount --}}
        <div class="lbl-bottom">
            <div class="lbl-courier">
                <div class="c-tag">&#128666; Courier</div>
                <div class="c-name {{ $cClass }}">
                    @if($courier === 'steadfast') &#128309; Steadfast
                    @elseif($courier === 'pathao') &#128311; Pathao
                    @elseif($courier === 'redx') &#128992; RedX
                    @elseif($courier) {{ ucfirst($courier) }}
                    @else <span style="color:#bbb;">— Not Assigned —</span>
                    @endif
                </div>
                @if($trkId)<div class="c-id">ID: {{ $trkId }}</div>@endif
                @if($order->courier_sent_at)
                    <div class="c-id" style="color:#888;">Sent: {{ \Carbon\Carbon::parse($order->courier_sent_at)->format('d M Y') }}</div>
                @endif
            </div>
            <div class="lbl-amount">
                <div class="a-tag">Total</div>
                <div class="a-val">&#2547;{{ number_format($order->amount,0) }}</div>
                <div class="a-qty">{{ $totalQty }} {{ $totalQty>1?'pcs':'pc' }}</div>
            </div>
        </div>

    </div>
    @endforeach

    @for($i = $chunk->count(); $i < 8; $i++)
    <div class="label empty"></div>
    @endfor

</div>
@endforeach

</body>
</html>
