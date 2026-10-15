@extends('backEnd.layouts.master')
@section('title', 'ইনকমপ্লিট অর্ডার')

@section('css')
@include('backEnd.incomplete_orders.partials.index_styles')
@endsection

@section('content')
<div class="container-fluid incomplete-orders-shell incomplete-orders-page">

    <div class="io-page-header">
        <div>
            <h4>ইনকমপ্লিট অর্ডার <span class="io-badge-count">{{ $orders->total() }}</span></h4>
            <div class="io-sub">চেকআউট শেষ না হওয়া কার্ট — গ্রাহক তথ্য ও পণ্য দেখতে সারি ট্যাপ করুন</div>
            <div class="io-info-pill">
                <i class="fas fa-shopping-cart"></i>
                গ্রহণ করলে রেগুলার অর্ডারে রূপান্তর হবে
            </div>
        </div>
    </div>

    <div class="io-card">
        <div class="io-card-head">
            <h6><i class="fas fa-hourglass-half"></i> অসম্পূর্ণ অর্ডার তালিকা</h6>
        </div>
        <div class="io-card-body">

            @if($orders->count() > 0)
            <p class="d-lg-none io-scroll-hint"><i class="fas fa-arrows-alt-h me-1"></i> বাকি কলাম দেখতে বাম–ডানে স্লাইড করুন</p>

            <div class="io-table-rail">
                <table class="table io-table mb-0">
                    <thead>
                        <tr>
                            <th width="44"></th>
                            <th>#</th>
                            <th>গ্রাহক</th>
                            <th>ফোন</th>
                            <th>তারিখ</th>
                            <th>মোট</th>
                            <th class="text-end">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="io-parent-row" onclick="toggleIoDetails({{ $order->id }})" id="io-row-{{ $order->id }}" data-order-id="{{ $order->id }}">
                            <td>
                                <span class="io-expand-btn" aria-hidden="true">
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </td>
                            <td>{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                            <td>
                                <span class="io-meta-name">{{ $order->name ?? 'অতিথি' }}</span><br>
                                <span class="io-meta-sub">#{{ $order->id }}</span>
                            </td>
                            <td>{{ $order->phone ?? '—' }}</td>
                            <td>
                                {{ optional($order->created_at)->format('d M, Y') }}<br>
                                <span class="io-meta-sub">{{ optional($order->created_at)->format('h:i A') }}</span>
                            </td>
                            <td>
                                <span class="io-amount">৳{{ number_format($order->total_amount ?? 0, 0) }}</span>
                            </td>
                            <td class="text-end" onclick="event.stopPropagation();">
                                <div class="io-row-actions">
                                    <form action="{{ route('admin.incomplete-orders.accept', $order->id) }}" method="POST"
                                          onsubmit="return confirm('এই অর্ডার গ্রহণ করে রেগুলার অর্ডারে রূপান্তর করবেন?');" class="d-inline">
                                        @csrf
                                        <button type="submit" class="io-act-btn io-act-accept" title="গ্রহণ" onclick="event.stopPropagation();">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.incomplete-orders.destroy', $order->id) }}" method="POST"
                                          onsubmit="return confirm('স্থায়ীভাবে মুছে ফেলবেন?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="io-act-btn io-act-delete" title="মুছুন" onclick="event.stopPropagation();">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <tr id="io-details-{{ $order->id }}" class="io-details-row">
                            <td colspan="7">
                                <div class="io-details-box">
                                    <div class="io-details-grid">
                                        <div>
                                            <div class="io-section-title">ডেলিভারি ঠিকানা</div>
                                            <p class="io-address">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $order->address ?? 'ঠিকানা দেওয়া হয়নি' }}
                                            </p>
                                        </div>
                                        <div>
                                            <div class="io-section-title">অর্ডার আইটেম</div>
                                            @php
                                                $items = $order->line_items;
                                                $meta = $order->checkout_meta;
                                            @endphp
                                            @if(!empty($meta['location_label']))
                                            <p class="small text-muted mb-2"><i class="fas fa-map-pin"></i> {{ $meta['location_label'] }}</p>
                                            @endif
                                            @if(!empty($items))
                                            <table class="io-items-table">
                                                <thead>
                                                    <tr>
                                                        <th width="52">ছবি</th>
                                                        <th>পণ্য</th>
                                                        <th>পরিমাণ</th>
                                                        <th class="text-end">দাম</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($items as $it)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                $img = $it['image'] ?? null;
                                                                if ($img && !\Illuminate\Support\Str::startsWith($img, ['http://', 'https://', '//'])) {
                                                                    $img = asset(ltrim($img, '/'));
                                                                }
                                                            @endphp
                                                            <img src="{{ $img ?: asset('public/no-image.png') }}"
                                                                 alt="" class="io-item-thumb"
                                                                 onerror="this.src='{{ asset('public/no-image.png') }}'">
                                                        </td>
                                                        <td>{{ \Illuminate\Support\Str::limit($it['name'] ?? 'পণ্য', 60) }}</td>
                                                        <td>×{{ $it['qty'] ?? 1 }}</td>
                                                        <td class="text-end fw-bold">৳{{ number_format($it['price'] ?? 0, 0) }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @elseif($order->product_link)
                                            <div class="io-product-fallback">
                                                @if($order->product_image)
                                                <img src="{{ asset($order->product_image) }}" alt="">
                                                @endif
                                                <a href="{{ $order->product_link }}" target="_blank" rel="noopener">পণ্য দেখুন</a>
                                            </div>
                                            @else
                                            <span class="text-muted small">কোনো পণ্যের বিস্তারিত নেই</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
            <div class="io-paginate">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
            @endif

            @else
            <div class="io-empty">
                <i class="fas fa-inbox"></i>
                <p class="mb-0">কোনো ইনকমপ্লিট অর্ডার নেই</p>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function toggleIoDetails(id) {
    var detailsRow = document.getElementById('io-details-' + id);
    var parentRow = document.getElementById('io-row-' + id);
    if (!detailsRow || !parentRow) return;

    var isOpen = detailsRow.classList.contains('io-open');
    if (isOpen) {
        detailsRow.classList.remove('io-open');
        parentRow.classList.remove('io-expanded');
    } else {
        detailsRow.classList.add('io-open');
        parentRow.classList.add('io-expanded');
    }
}
</script>
@endsection
