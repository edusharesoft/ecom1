@php
    $gccEnabled = app(\App\Services\GeminiCustomerContextService::class)->isEnabled();
    $gccWelcome = app(\App\Services\GeminiCustomerContextService::class)->welcomeMessage();
    $gccHistory = session('gemini_customer_chat_history', []);
    $gccCustomer = auth('customer')->user();
@endphp

@if($gccEnabled)
<style>
#gcc-widget { font-family: inherit; }
#gcc-widget * { box-sizing: border-box; }

#gcc-toggle {
    position: fixed; right: 20px; bottom: 75px; z-index: 99990;
    width: 58px; height: 58px; border-radius: 50%; border: none; cursor: pointer;
    background: #1a9a5c;
    color: #fff; box-shadow: 0 8px 28px rgba(26,154,92,.45);
    display: flex; align-items: center; justify-content: center;
    transition: transform .2s, box-shadow .2s, opacity .2s;
    padding: 0;
}
#gcc-toggle:hover { transform: scale(1.06); box-shadow: 0 10px 32px rgba(26,154,92,.5); }
#gcc-toggle.open { opacity: 0; pointer-events: none; transform: scale(.9); }
#gcc-toggle .gcc-toggle-icon { width: 28px; height: 28px; display: block; }

.gcc-headset-icon { width: 22px; height: 22px; display: block; }
.gcc-header-icon-wrap {
    width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
    background: #1a9a5c; color: #fff;
    display: flex; align-items: center; justify-content: center;
}
.gcc-online-dot {
    width: 8px; height: 8px; border-radius: 50%; background: #22c55e;
    display: inline-block; margin-right: 6px;
    box-shadow: 0 0 0 3px rgba(34,197,94,.25);
    animation: gcc-pulse 2s infinite;
}
@keyframes gcc-pulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
    50% { box-shadow: 0 0 0 6px rgba(34,197,94,.12); }
}

#gcc-panel {
    position: fixed; right: 20px; bottom: 145px; z-index: 99989;
    width: 380px; max-width: calc(100vw - 24px); height: 520px; max-height: calc(100vh - 160px);
    background: #fff; border-radius: 16px;
    box-shadow: 0 20px 60px rgba(15,23,42,.2);
    display: flex; flex-direction: column; overflow: hidden;
    transform: scale(.9) translateY(20px); opacity: 0; pointer-events: none;
    transition: all .28s cubic-bezier(.4,0,.2,1);
}
#gcc-panel.open { transform: scale(1) translateY(0); opacity: 1; pointer-events: auto; }

#gcc-header {
    background: #fff; color: #1e293b; padding: 14px 16px; flex-shrink: 0;
    border-bottom: 1px solid #e2e8f0;
}
#gcc-header h5 { color: #0f172a !important; margin: 0; font-size: 16px; font-weight: 700; }
#gcc-header .gcc-header-sub { color: #64748b !important; margin: 4px 0 0; font-size: 12px; display: flex; align-items: center; }
#gcc-header-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; }
#gcc-close {
    background: #f1f5f9; border: none; color: #64748b; font-size: 18px; cursor: pointer;
    line-height: 1; padding: 6px 10px; border-radius: 8px;
}

#gcc-chips { display: flex; flex-wrap: wrap; gap: 6px; padding: 10px 12px; background: #f0fdf4; border-bottom: 1px solid #dcfce7; }
#gcc-chips button {
    border: 1px solid #86efac; background: #fff; color: #166534;
    border-radius: 999px; padding: 5px 10px; font-size: 11px; cursor: pointer;
}

#gcc-messages { flex: 1; overflow-y: auto; padding: 14px; background: #f8fafc; }
.gcc-msg { display: flex; gap: 8px; margin-bottom: 12px; }
.gcc-msg.user { flex-direction: row-reverse; }
.gcc-avatar {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 12px;
}
.gcc-msg.user .gcc-avatar { background: #6366f1; color: #fff; }
.gcc-msg.bot .gcc-avatar {
    background: #1a9a5c; color: #fff; width: 32px; height: 32px;
}
.gcc-bubble {
    max-width: 82%; padding: 10px 12px; border-radius: 14px;
    font-size: 13px; line-height: 1.55; white-space: pre-wrap; word-break: break-word;
}
.gcc-msg.user .gcc-bubble { background: #6366f1; color: #fff; border-bottom-right-radius: 4px; }
.gcc-msg.bot .gcc-bubble { background: #fff; border: 1px solid #e2e8f0; color: #1e293b; border-bottom-left-radius: 4px; }

.gcc-products { display: flex; flex-direction: column; gap: 8px; margin-top: 8px; max-width: 88%; }
.gcc-product-card {
    display: flex; gap: 10px; background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 8px; text-decoration: none; color: inherit;
    transition: box-shadow .2s;
}
.gcc-product-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); color: inherit; }
.gcc-product-card img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
.gcc-product-card .gcc-pname { font-size: 12px; font-weight: 600; color: #1e293b; margin: 0 0 3px; }
.gcc-product-card .gcc-pprice { font-size: 13px; font-weight: 700; color: #059669; margin: 0; }
.gcc-product-card .gcc-pstock { font-size: 10px; color: #64748b; margin: 2px 0 0; }

#gcc-typing { display: none; padding: 0 14px 8px; font-size: 12px; color: #64748b; }
#gcc-typing.show { display: block; }

#gcc-input-wrap { padding: 10px 12px; border-top: 1px solid #e2e8f0; background: #fff; }
#gcc-form { display: flex; gap: 8px; align-items: flex-end; }
#gcc-input {
    flex: 1; resize: none; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 10px 12px; font-size: 13px; max-height: 90px; min-height: 40px;
}
#gcc-send {
    background: linear-gradient(135deg, #059669, #10b981);
    border: none; color: #fff; border-radius: 12px; padding: 10px 14px;
    font-weight: 600; font-size: 13px; cursor: pointer;
}
#gcc-send:disabled { opacity: .6; cursor: not-allowed; }

#gcc-complaint-panel {
    display: none; position: absolute; inset: 0; background: #fff; z-index: 5;
    flex-direction: column; padding: 16px; overflow-y: auto;
}
#gcc-complaint-panel.show { display: flex; }
#gcc-complaint-panel h6 { font-weight: 700; margin-bottom: 12px; }
#gcc-complaint-panel label { font-size: 12px; font-weight: 600; color: #475569; }
#gcc-complaint-panel input, #gcc-complaint-panel textarea {
    width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; font-size: 13px; margin-bottom: 10px;
}
#gcc-complaint-actions { display: flex; gap: 8px; margin-top: 8px; }
#gcc-complaint-actions button { flex: 1; padding: 10px; border-radius: 10px; font-weight: 600; font-size: 13px; cursor: pointer; border: none; }
#gcc-complaint-submit { background: #059669; color: #fff; }
#gcc-complaint-cancel { background: #f1f5f9; color: #475569; }

@media (max-width: 480px) {
    #gcc-panel { right: 12px; bottom: 130px; width: calc(100vw - 24px); height: 65vh; }
    #gcc-toggle { right: 16px; bottom: 70px; width: 54px; height: 54px; }
    #gcc-toggle .gcc-toggle-icon { width: 26px; height: 26px; }
}
</style>

<div id="gcc-widget">
    <div id="gcc-panel">
        <div id="gcc-header">
            <div id="gcc-header-top">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div class="gcc-header-icon-wrap" aria-hidden="true">
                        <svg class="gcc-headset-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 11h2a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H3v-5z"/>
                            <path d="M21 11h-2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h2v-5z"/>
                            <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                        </svg>
                    </div>
                    <div>
                        <h5>লাইভ সহায়তা</h5>
                        <p class="gcc-header-sub"><span class="gcc-online-dot"></span>অনলাইন সাহায্য চাইলে ক্লিক করুন</p>
                    </div>
                </div>
                <button type="button" id="gcc-close" aria-label="Close">✕</button>
            </div>
        </div>

        <div id="gcc-chips">
            <button type="button" data-q="৫০০০ টাকার মধ্যে ভালো প্রোডাক্ট দেখান">🔍 প্রোডাক্ট খুঁজুন</button>
            <button type="button" data-q="আমার অর্ডার ট্র্যাক করতে চাই">📦 অর্ডার ট্র্যাক</button>
            <button type="button" id="gcc-open-complaint">📝 কমপ্লেইন</button>
            <button type="button" data-q="রিফান্ড কিভাবে করব?">💰 রিফান্ড</button>
        </div>

        <div id="gcc-messages"></div>
        <div id="gcc-typing">ভাবছে...</div>

        <div id="gcc-input-wrap">
            <form id="gcc-form">
                <textarea id="gcc-input" rows="1" placeholder="মেসেজ লিখুন..." maxlength="2000"></textarea>
                <button type="submit" id="gcc-send">➤</button>
            </form>
        </div>

        <div id="gcc-complaint-panel">
            <h6>কমপ্লেইন / সমস্যা জানান</h6>
            <label>নাম *</label>
            <input type="text" id="gcc-c-name" value="{{ $gccCustomer->name ?? '' }}" maxlength="255">
            <label>মোবাইল *</label>
            <input type="text" id="gcc-c-phone" value="{{ $gccCustomer->phone ?? '' }}" maxlength="20">
            <label>অর্ডার/ইনভয়েস (ঐচ্ছিক)</label>
            <input type="text" id="gcc-c-order" maxlength="50">
            <label>বিস্তারিত *</label>
            <textarea id="gcc-c-desc" rows="4" maxlength="5000" placeholder="সমস্যাটি লিখুন..."></textarea>
            <div id="gcc-complaint-actions">
                <button type="button" class="gcc-complaint-cancel" id="gcc-complaint-cancel">বাতিল</button>
                <button type="button" class="gcc-complaint-submit" id="gcc-complaint-submit">জমা দিন</button>
            </div>
        </div>
    </div>

    <button type="button" id="gcc-toggle" title="লাইভ সহায়তা" aria-label="Open live chat">
        <svg class="gcc-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            <path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/>
        </svg>
    </button>
</div>

<script>
(function () {
    var history = @json($gccHistory);
    var welcome = @json($gccWelcome);
    var routes = {
        send: @json(route('customer.gemini_chat.send')),
        clear: @json(route('customer.gemini_chat.clear')),
        complaint: @json(route('customer.gemini_chat.complaint')),
        csrf: @json(csrf_token())
    };
    var chatSvg = '<svg class="gcc-toggle-icon" style="width:16px;height:16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01"/><path d="M12 10h.01"/><path d="M16 10h.01"/></svg>';

    var panel = document.getElementById('gcc-panel');
    var toggle = document.getElementById('gcc-toggle');
    var messages = document.getElementById('gcc-messages');
    var typing = document.getElementById('gcc-typing');
    var input = document.getElementById('gcc-input');
    var form = document.getElementById('gcc-form');
    var sendBtn = document.getElementById('gcc-send');
    var isOpen = false;
    var welcomed = history.length > 0;

    function esc(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

    function renderProducts(products) {
        if (!products || !products.length) return '';
        var html = '<div class="gcc-products">';
        products.slice(0, 5).forEach(function (p) {
            html += '<a class="gcc-product-card" href="' + esc(p.url) + '" target="_blank">';
            if (p.image) html += '<img src="' + esc(p.image) + '" alt="">';
            html += '<div><p class="gcc-pname">' + esc(p.name) + '</p>';
            html += '<p class="gcc-pprice">৳' + esc(String(p.price)) + '</p>';
            html += '<p class="gcc-pstock">' + (p.in_stock ? '✓ স্টকে আছে' : 'স্টক শেষ') + '</p></div></a>';
        });
        return html + '</div>';
    }

    function appendBubble(role, text, products, scroll) {
        var div = document.createElement('div');
        div.className = 'gcc-msg ' + (role === 'user' ? 'user' : 'bot');
        var avatar = role === 'user' ? '👤' : chatSvg;
        var extra = role === 'bot' && products ? renderProducts(products) : '';
        div.innerHTML = '<div class="gcc-avatar">' + avatar + '</div><div><div class="gcc-bubble">' + esc(text) + '</div>' + extra + '</div>';
        messages.appendChild(div);
        if (scroll !== false) messages.scrollTop = messages.scrollHeight;
    }

    function renderHistory() {
        messages.innerHTML = '';
        if (!history.length) {
            appendBubble('bot', welcome, null, false);
            welcomed = true;
            return;
        }
        history.forEach(function (m) {
            appendBubble(m.role === 'user' ? 'user' : 'bot', m.text, m.products || null, false);
        });
        messages.scrollTop = messages.scrollHeight;
    }

    function openPanel() {
        isOpen = true;
        panel.classList.add('open');
        toggle.classList.add('open');
        if (!welcomed) { appendBubble('bot', welcome); welcomed = true; }
        setTimeout(function () { input.focus(); }, 200);
    }

    function closePanel() {
        isOpen = false;
        panel.classList.remove('open');
        toggle.classList.remove('open');
    }

    function sendMessage(text) {
        text = (text || '').trim();
        if (!text) return;
        appendBubble('user', text);
        history.push({ role: 'user', text: text });
        input.value = '';
        sendBtn.disabled = true;
        typing.classList.add('show');

        fetch(routes.send, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ message: text })
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) {
            if (res.ok && res.data.success) {
                appendBubble('bot', res.data.reply, res.data.products || []);
                history.push({ role: 'model', text: res.data.reply, products: res.data.products });
            } else {
                appendBubble('bot', '⚠️ ' + ((res.data && res.data.message) ? res.data.message : 'ব্যর্থ হয়েছে'));
                history.pop();
            }
        })
        .catch(function () {
            appendBubble('bot', '⚠️ সংযোগ ব্যর্থ। আবার চেষ্টা করুন।');
            history.pop();
        })
        .finally(function () {
            sendBtn.disabled = false;
            typing.classList.remove('show');
        });
    }

    toggle.addEventListener('click', function () { isOpen ? closePanel() : openPanel(); });
    document.getElementById('gcc-close').addEventListener('click', closePanel);
    form.addEventListener('submit', function (e) { e.preventDefault(); sendMessage(input.value); });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.dispatchEvent(new Event('submit')); }
    });

    document.querySelectorAll('#gcc-chips [data-q]').forEach(function (btn) {
        btn.addEventListener('click', function () { sendMessage(btn.getAttribute('data-q')); });
    });

    document.getElementById('gcc-open-complaint').addEventListener('click', function () {
        document.getElementById('gcc-complaint-panel').classList.add('show');
    });
    document.getElementById('gcc-complaint-cancel').addEventListener('click', function () {
        document.getElementById('gcc-complaint-panel').classList.remove('show');
    });
    document.getElementById('gcc-complaint-submit').addEventListener('click', function () {
        var payload = {
            name: document.getElementById('gcc-c-name').value.trim(),
            phone: document.getElementById('gcc-c-phone').value.trim(),
            order_id: document.getElementById('gcc-c-order').value.trim(),
            description: document.getElementById('gcc-c-desc').value.trim()
        };
        if (!payload.name || !payload.phone || !payload.description) {
            alert('নাম, মোবাইল ও বিস্তারিত পূরণ করুন।');
            return;
        }
        fetch(routes.complaint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (d) {
            document.getElementById('gcc-complaint-panel').classList.remove('show');
            appendBubble('bot', d.success ? d.message : ('⚠️ ' + (d.message || 'ব্যর্থ')));
            if (d.success) {
                document.getElementById('gcc-c-desc').value = '';
            }
        });
    });

    renderHistory();
})();
</script>
@endif
