@foreach ($reviews as $review)
<div class="col-12 product-review-item">
    <div class="gomobd-review-card shadow-sm">
        <div class="gomobd-review-card-header d-flex justify-content-between align-items-start flex-wrap">
            <div class="d-flex align-items-center">
                <div class="gomobd-review-avatar">
                    {{ strtoupper(substr($review->name, 0, 1)) }}
                </div>
                <div class="gomobd-review-meta">
                    <h6 class="gomobd-review-name">{{ $review->name }}</h6>
                    <small class="gomobd-review-date">{{ $review->created_at->format('d M Y') }}</small>
                </div>
            </div>
            <div class="gomobd-review-stars">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $review->ratting)
                        <i class="fa-solid fa-star"></i>
                    @else
                        <i class="fa-regular fa-star"></i>
                    @endif
                @endfor
            </div>
        </div>
        <div class="gomobd-review-body mt-2">
            <p><i class="fa-regular fa-comment-dots text-success me-1"></i> {{ $review->review }}</p>
        </div>
    </div>
</div>
@endforeach
