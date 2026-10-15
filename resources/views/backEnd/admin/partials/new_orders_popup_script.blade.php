<script>
(function () {
    var popupUrl = @json(route('admin.dashboard.new_orders_popup'));
    var dismissUrl = @json(route('admin.dashboard.dismiss_new_orders_popup'));
    var csrfToken = @json(csrf_token());

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
                gain.gain.exponentialRampToValueAtTime(vol || 0.25, start + 0.03);
                gain.gain.exponentialRampToValueAtTime(0.0001, start + dur);
                osc.start(start);
                osc.stop(start + dur + 0.05);
            }
            var t = ctx.currentTime;
            tone(523.25, t, 0.2, 0.3);
            tone(659.25, t + 0.22, 0.2, 0.3);
            tone(783.99, t + 0.44, 0.35, 0.35);
            tone(1046.5, t + 0.72, 0.4, 0.28);
            setTimeout(function () {
                if (ctx.state !== 'closed') ctx.close();
            }, 2000);
        } catch (e) {}
    }

    function dismissPopup() {
        return fetch(dismissUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        }).catch(function () {});
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderOrders(orders) {
        var list = document.getElementById('nopOrderList');
        var badge = document.getElementById('nopCountBadge');
        if (!list) return;
        list.innerHTML = '';
        if (badge) badge.textContent = orders.length;

        orders.forEach(function (o) {
            var li = document.createElement('li');
            li.className = 'nop-item';
            var main = document.createElement('div');
            main.className = 'nop-item-main';
            var rowTop = document.createElement('div');
            rowTop.className = 'nop-row-top';
            var inv = document.createElement('div');
            inv.className = 'nop-inv';
            inv.textContent = '#' + (o.invoice_id || '');
            var name = document.createElement('div');
            name.className = 'nop-name';
            name.textContent = o.customer_name || '—';
            var meta = document.createElement('div');
            meta.className = 'nop-meta';
            meta.textContent = (o.phone || '') + ' · ' + (o.created_at || '');
            var st = document.createElement('span');
            st.className = 'nop-status';
            st.textContent = o.status_name || '—';
            rowTop.appendChild(inv);
            rowTop.appendChild(st);
            main.appendChild(rowTop);
            main.appendChild(name);
            main.appendChild(meta);

            var right = document.createElement('div');
            right.className = 'nop-item-side';
            var amt = document.createElement('div');
            amt.className = 'nop-amount';
            amt.textContent = '৳' + (o.amount || '0');
            var link = document.createElement('a');
            link.href = o.process_url || '#';
            link.className = 'nop-open';
            link.textContent = 'প্রসেস';
            right.appendChild(amt);
            right.appendChild(link);

            li.appendChild(main);
            li.appendChild(right);
            list.appendChild(li);
        });
    }

    function showModal() {
        var el = document.getElementById('newOrdersModal');
        if (!el || typeof bootstrap === 'undefined') return;
        var modal = bootstrap.Modal.getOrCreateInstance(el);
        modal.show();
    }

    function bindDismiss() {
        var el = document.getElementById('newOrdersModal');
        if (el) {
            el.addEventListener('hidden.bs.modal', function () {
                dismissPopup();
            }, { once: true });
        }
    }

    function init() {
        fetch(popupUrl, {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data && data.latest_id && window.AdminOrderNotify && window.AdminOrderNotify.setBaseline) {
                window.AdminOrderNotify.setBaseline(data.latest_id);
            }
            if (!data || !data.show || !data.orders || !data.orders.length) {
                dismissPopup();
                return;
            }
            if (window.AdminOrderNotify && window.AdminOrderNotify.setBaseline) {
                var maxId = data.latest_id || 0;
                data.orders.forEach(function (o) {
                    if (o.id && o.id > maxId) maxId = o.id;
                });
                window.AdminOrderNotify.setBaseline(maxId);
            }
            renderOrders(data.orders);
            bindDismiss();
            showModal();
            playOrderRingtone();
            setTimeout(playOrderRingtone, 900);
        })
        .catch(function () {});
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
