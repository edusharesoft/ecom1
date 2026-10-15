{{-- লাইভ নতুন অর্ডার টোস্ট (সব অ্যাডমিন পেজ) --}}
<style>
    .aot-stack {
        position: fixed;
        bottom: 20px;
        right: 16px;
        z-index: 1080;
        display: flex;
        flex-direction: column-reverse;
        gap: 10px;
        max-width: min(360px, calc(100vw - 32px));
        pointer-events: none;
    }
    .aot-toast {
        pointer-events: auto;
        position: relative;
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.14), 0 0 0 1px rgba(148, 163, 184, 0.12);
        cursor: pointer;
        animation: aot-pop-in 0.45s cubic-bezier(0.34, 1.4, 0.64, 1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .aot-toast:hover {
        transform: translateY(-2px) scale(1.01);
        box-shadow: 0 20px 48px rgba(79, 70, 229, 0.2), 0 0 0 1px rgba(99, 102, 241, 0.2);
    }
    .aot-toast.aot-out {
        animation: aot-pop-out 0.32s ease-in forwards;
    }
    @keyframes aot-pop-in {
        from { opacity: 0; transform: translateX(24px) scale(0.92); }
        to { opacity: 1; transform: translateX(0) scale(1); }
    }
    @keyframes aot-pop-out {
        from { opacity: 1; transform: translateX(0) scale(1); }
        to { opacity: 0; transform: translateX(24px) scale(0.92); }
    }
    .aot-toast::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #4f46e5, #06b6d4, #10b981);
    }
    .aot-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 2px;
        background: linear-gradient(90deg, #4f46e5, #818cf8);
        width: 100%;
        transform-origin: left;
        animation: aot-progress-shrink linear forwards;
    }
    @keyframes aot-progress-shrink {
        from { transform: scaleX(1); }
        to { transform: scaleX(0); }
    }
    .aot-inner {
        display: flex;
        gap: 12px;
        padding: 12px 12px 14px;
    }
    .aot-thumbs {
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
    }
    .aot-thumb-grid {
        display: grid;
        grid-template-columns: repeat(2, 40px);
        gap: 4px;
    }
    .aot-thumb-grid.aot-single {
        grid-template-columns: 48px;
    }
    .aot-thumb-wrap {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        background: #f1f5f9;
    }
    .aot-thumb-grid.aot-single .aot-thumb-wrap {
        width: 48px;
        height: 48px;
    }
    .aot-thumb-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .aot-thumb-more {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .aot-body { flex: 1; min-width: 0; }
    .aot-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }
    .aot-label-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .aot-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
        animation: aot-dot-pulse 1.5s ease-in-out infinite;
    }
    @keyframes aot-dot-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .aot-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
    }
    .aot-close {
        background: #f1f5f9;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 8px;
        color: #64748b;
        font-size: 14px;
        line-height: 1;
        cursor: pointer;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s, color 0.15s;
    }
    .aot-close:hover {
        background: #fee2e2;
        color: #dc2626;
    }
    .aot-inv {
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    .aot-inv span { color: #4f46e5; }
    .aot-name {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .aot-meta {
        font-size: 11px;
        color: #94a3b8;
        margin-top: 1px;
    }
    .aot-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px dashed #e2e8f0;
    }
    .aot-status {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .aot-amt {
        font-size: 14px;
        font-weight: 800;
        color: #059669;
        white-space: nowrap;
    }
    .aot-cta {
        font-size: 10px;
        font-weight: 600;
        color: #4f46e5;
        display: flex;
        align-items: center;
        gap: 4px;
    }
</style>

<div id="adminOrderToastStack" class="aot-stack" aria-live="polite" aria-label="নতুন অর্ডার নোটিফিকেশন"></div>

<script>
(function () {
    var pollUrl = @json(route('admin.orders.poll_new'));
    var fallbackImg = @json(asset('public/no-image.png'));
    var STORAGE_KEY = 'admin_last_order_id';
    var POLL_MS = 15000;
    var MAX_TOASTS = 4;
    var TOAST_TTL_MS = 12000;

    var lastId = parseInt(localStorage.getItem(STORAGE_KEY) || '0', 10) || 0;
    var polling = false;

    function playOrderRingtone() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            var ctx = new Ctx();
            function tone(freq, start, dur, vol) {
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                osc.connect(gain);
                gain.connect(ctx.destination);
                gain.gain.setValueAtTime(0.0001, start);
                gain.gain.exponentialRampToValueAtTime(vol || 0.22, start + 0.03);
                gain.gain.exponentialRampToValueAtTime(0.0001, start + dur);
                osc.start(start);
                osc.stop(start + dur + 0.05);
            }
            var t = ctx.currentTime;
            tone(523.25, t, 0.18, 0.28);
            tone(659.25, t + 0.2, 0.18, 0.28);
            tone(783.99, t + 0.4, 0.3, 0.3);
            setTimeout(function () {
                if (ctx.state !== 'closed') ctx.close();
            }, 1500);
        } catch (e) {}
    }

    function setBaseline(id) {
        lastId = parseInt(id, 10) || 0;
        if (lastId > 0) localStorage.setItem(STORAGE_KEY, String(lastId));
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function removeToast(el) {
        if (!el) return;
        el.classList.add('aot-out');
        setTimeout(function () {
            if (el.parentNode) el.parentNode.removeChild(el);
        }, 300);
    }

    function buildThumbs(products, moreCount) {
        var thumbs = document.createElement('div');
        thumbs.className = 'aot-thumbs';
        var grid = document.createElement('div');
        var list = products && products.length ? products : [];
        grid.className = 'aot-thumb-grid' + (list.length === 1 ? ' aot-single' : '');
        list.slice(0, 4).forEach(function (p, idx) {
            var wrap = document.createElement('div');
            wrap.className = 'aot-thumb-wrap';
            var img = document.createElement('img');
            img.src = p.image || fallbackImg;
            img.alt = p.name || '';
            img.onerror = function () { this.src = fallbackImg; };
            wrap.appendChild(img);
            if (idx === 3 && moreCount > 0) {
                var more = document.createElement('span');
                more.className = 'aot-thumb-more';
                more.textContent = '+' + moreCount;
                wrap.appendChild(more);
            }
            grid.appendChild(wrap);
        });
        if (!list.length) {
            var wrap = document.createElement('div');
            wrap.className = 'aot-thumb-wrap';
            var img = document.createElement('img');
            img.src = fallbackImg;
            img.alt = '';
            wrap.appendChild(img);
            grid.className = 'aot-thumb-grid aot-single';
            grid.appendChild(wrap);
        }
        thumbs.appendChild(grid);
        return thumbs;
    }

    function showToast(order) {
        var stack = document.getElementById('adminOrderToastStack');
        if (!stack) return;

        while (stack.children.length >= MAX_TOASTS) {
            removeToast(stack.firstElementChild);
        }

        var el = document.createElement('div');
        el.className = 'aot-toast';
        el.setAttribute('role', 'alert');

        var progress = document.createElement('div');
        progress.className = 'aot-progress';
        progress.style.animationDuration = (TOAST_TTL_MS / 1000) + 's';
        el.appendChild(progress);

        var inner = document.createElement('div');
        inner.className = 'aot-inner';
        inner.appendChild(buildThumbs(order.products || [], order.more_count || 0));

        var body = document.createElement('div');
        body.className = 'aot-body';

        var top = document.createElement('div');
        top.className = 'aot-top';
        var labelRow = document.createElement('div');
        labelRow.className = 'aot-label-row';
        var dot = document.createElement('span');
        dot.className = 'aot-dot';
        var title = document.createElement('span');
        title.className = 'aot-title';
        title.textContent = 'নতুন অর্ডার';
        labelRow.appendChild(dot);
        labelRow.appendChild(title);
        var closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'aot-close';
        closeBtn.setAttribute('aria-label', 'বন্ধ');
        closeBtn.innerHTML = '&times;';
        top.appendChild(labelRow);
        top.appendChild(closeBtn);

        var inv = document.createElement('div');
        inv.className = 'aot-inv';
        inv.innerHTML = '<span>#</span>' + escapeHtml(order.invoice_id);

        var name = document.createElement('div');
        name.className = 'aot-name';
        name.textContent = order.customer_name || '—';

        var meta = document.createElement('div');
        meta.className = 'aot-meta';
        meta.textContent = (order.phone || '') + (order.created_at ? ' · ' + order.created_at : '');

        var foot = document.createElement('div');
        foot.className = 'aot-footer';
        var status = document.createElement('span');
        status.className = 'aot-status';
        status.textContent = order.status_name || '—';
        var amt = document.createElement('span');
        amt.className = 'aot-amt';
        amt.textContent = '৳' + (order.amount || '0');
        foot.appendChild(status);
        foot.appendChild(amt);

        body.appendChild(top);
        body.appendChild(inv);
        body.appendChild(name);
        body.appendChild(meta);
        body.appendChild(foot);
        inner.appendChild(body);
        el.appendChild(inner);

        closeBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            removeToast(el);
        });
        el.addEventListener('click', function (e) {
            if (e.target.closest('.aot-close')) return;
            if (order.process_url) window.location.href = order.process_url;
        });

        stack.appendChild(el);
        setTimeout(function () { removeToast(el); }, TOAST_TTL_MS);
    }

    function handleNewOrders(orders, latestId) {
        if (!orders || !orders.length) {
            if (latestId) setBaseline(latestId);
            return;
        }
        orders.forEach(function (o) { showToast(o); });
        playOrderRingtone();
        if (latestId) setBaseline(latestId);
    }

    function poll() {
        if (polling) return;
        polling = true;
        var url = pollUrl + (lastId > 0 ? ('?after_id=' + lastId) : '?init=1');
        fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data) return;
            if (data.init && data.latest_id !== undefined) {
                setBaseline(data.latest_id);
                return;
            }
            if (data.orders && data.orders.length) {
                handleNewOrders(data.orders, data.latest_id);
            } else if (data.latest_id) {
                setBaseline(data.latest_id);
            }
        })
        .catch(function () {})
        .finally(function () { polling = false; });
    }

    window.AdminOrderNotify = {
        setBaseline: setBaseline,
        getBaseline: function () { return lastId; },
        pollNow: poll
    };

    function start() {
        poll();
        setInterval(poll, POLL_MS);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
</script>
