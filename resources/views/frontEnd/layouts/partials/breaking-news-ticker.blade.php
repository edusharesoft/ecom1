@php
    $breakingNewsText = trim(preg_replace('/\s+/u', ' ', (string) (optional($generalsetting)->top_headline ?? '')));
    $showNewsTicker = $breakingNewsText !== ''
        && (int) (optional($generalsetting)->news_ticker_enabled ?? 0) === 1;
    $tickerBg = optional($generalsetting)->primary_color ?? '#0d6efd';
    $tickerTextCol = '#ffffff';
    $tickerLabelBg = optional($generalsetting)->secodery_color ?? '#198754';
    $tickerLabelTxt = '#ffffff';
    $tickerSpeed = max(20, min(50, (int) (strlen($breakingNewsText) / 4)));
@endphp
@if($showNewsTicker)
<div class="news-ticker-bar" id="newsTickerBar" data-text="{{ e($breakingNewsText) }}" style="background:{{ $tickerBg }}; color:{{ $tickerTextCol }};">
    <div class="news-ticker-label" style="background:{{ $tickerLabelBg }}; color:{{ $tickerLabelTxt }};">
        <i class="fas fa-bolt" style="margin-right:5px;"></i> Speacial Notice
    </div>
    <div class="news-ticker-track">
        <div class="news-ticker-inner" id="newsTickerInner" style="animation-duration: {{ $tickerSpeed }}s;">
            @foreach([1, 2] as $copy)
                <span class="news-ticker-item" style="color:{{ $tickerTextCol }};">{{ $breakingNewsText }}</span>
            @endforeach
        </div>
    </div>
    <button type="button" class="news-ticker-close" id="newsTickerClose" title="Close" style="color:{{ $tickerTextCol }};">
        <i class="fas fa-times"></i>
    </button>
</div>
<style>
    #newsTickerBar,
    #newsTickerBar .news-ticker-item,
    #newsTickerBar .news-ticker-item a,
    #newsTickerBar .news-ticker-close,
    #newsTickerBar .news-ticker-label {
        color: #ffffff !important;
    }
    #newsTickerBar .news-ticker-label::after {
        border-left-color: {{ $tickerLabelBg }} !important;
    }
    #newsTickerBar .news-ticker-item::after {
        display: none;
    }
    #newsTickerBar .news-ticker-close:hover {
        color: #ffffff !important;
        opacity: 0.85;
    }
</style>
<script>
(function () {
    var closeBtn = document.getElementById('newsTickerClose');
    var tickerBar = document.getElementById('newsTickerBar');
    var content = document.getElementById('content');
    if (!closeBtn || !tickerBar) return;

    var text = tickerBar.getAttribute('data-text') || '';
    var storageKey = 'news_ticker_closed_' + text.replace(/\s+/g, '_').slice(0, 80);

    try {
        if (sessionStorage.getItem(storageKey) === '1') {
            tickerBar.style.display = 'none';
            return;
        }
    } catch (e) {}

    closeBtn.addEventListener('click', function () {
        tickerBar.style.display = 'none';
        if (content) {
            var currentPt = parseInt(window.getComputedStyle(content).paddingTop, 10) || 0;
            var tickerH = window.innerWidth <= 767 ? 30 : 34;
            content.style.paddingTop = Math.max(0, currentPt - tickerH) + 'px';
        }
        try {
            sessionStorage.setItem(storageKey, '1');
        } catch (e) {}
    });
})();
</script>
@endif
