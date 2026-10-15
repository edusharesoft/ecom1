@php
    $geminiWidgetHistory = session('gemini_admin_chat_history', []);
@endphp

<style>
#gemini-admin-widget { --gaw-primary: #6366f1; --gaw-dark: #312e81; font-family: inherit; }
#gemini-admin-widget * { box-sizing: border-box; }

#gaw-toggle {
    position: fixed;
    right: 22px;
    bottom: 88px;
    z-index: 99990;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    background: linear-gradient(135deg, #7c3aed, #6366f1);
    color: #fff;
    box-shadow: 0 8px 28px rgba(99, 102, 241, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: transform 0.2s, box-shadow 0.2s;
}
#gaw-toggle:hover { transform: scale(1.05); box-shadow: 0 10px 32px rgba(99, 102, 241, 0.55); }
#gaw-toggle.open { background: #475569; }

#gaw-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 400px;
    max-width: 100vw;
    height: 100vh;
    z-index: 99989;
    background: #fff;
    box-shadow: -8px 0 40px rgba(15, 23, 42, 0.15);
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.32s cubic-bezier(0.4, 0, 0.2, 1);
}
#gaw-panel.open { transform: translateX(0); }

#gaw-header {
    background: linear-gradient(135deg, #1e1b4b, #4c1d95);
    color: #fff;
    padding: 16px 18px;
    flex-shrink: 0;
}
#gaw-header h5,
#gaw-header p,
#gaw-header .gaw-title-text {
    color: #ffffff !important;
}
#gaw-header h5 { margin: 0; font-size: 15px; font-weight: 700; }
#gaw-header p { margin: 4px 0 0; font-size: 11px; opacity: 1; }
#gaw-header .gaw-title-row {
    display: flex;
    align-items: center;
    gap: 8px;
}
.gaw-bot-icon {
    width: 22px;
    height: 22px;
    flex-shrink: 0;
    display: block;
    color: #ffffff;
    stroke: #ffffff;
}
#gaw-toggle .gaw-bot-icon {
    width: 32px;
    height: 32px;
    stroke-width: 2.2;
}
#gaw-messages .gaw-bot-icon { width: 18px; height: 18px; }
#gaw-header-actions { display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; }
#gaw-header-actions button, #gaw-header-actions a {
    font-size: 11px;
    padding: 5px 10px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.35);
    background: rgba(255,255,255,0.12);
    color: #fff;
    cursor: pointer;
    text-decoration: none;
    line-height: 1.3;
}
#gaw-header-actions button:hover, #gaw-header-actions a:hover { background: rgba(255,255,255,0.22); color: #fff; }

#gaw-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8fafc;
}
#gaw-messages .gaw-msg { display: flex; gap: 8px; margin-bottom: 12px; }
#gaw-messages .gaw-msg.user { flex-direction: row-reverse; }
#gaw-messages .gaw-avatar {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: 13px;
}
#gaw-messages .gaw-msg.user .gaw-avatar { background: #6366f1; color: #fff; }
#gaw-messages .gaw-msg.bot .gaw-avatar { background: #a855f7; color: #fff; }
#gaw-messages .gaw-bubble {
    max-width: 82%; padding: 10px 12px; border-radius: 14px;
    font-size: 13px; line-height: 1.5; white-space: pre-wrap; word-break: break-word;
}
#gaw-messages .gaw-msg.user .gaw-bubble {
    background: linear-gradient(135deg, #6366f1, #7c3aed); color: #fff;
    border-bottom-right-radius: 4px;
}
#gaw-messages .gaw-msg.bot .gaw-bubble {
    background: #fff; border: 1px solid #e2e8f0; color: #1e293b;
    border-bottom-left-radius: 4px;
}
#gaw-empty { text-align: center; padding: 30px 12px; color: #64748b; font-size: 13px; }
#gaw-suggestions { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-top: 12px; }
#gaw-suggestions button {
    border: 1px solid #c7d2fe; background: #eef2ff; color: #4338ca;
    border-radius: 999px; padding: 6px 12px; font-size: 11px; cursor: pointer;
}

#gaw-typing {
    display: none; padding: 0 16px 8px; font-size: 12px; color: #64748b; background: #f8fafc;
}
#gaw-typing.show { display: block; }

#gaw-input-wrap {
    padding: 12px 14px;
    border-top: 1px solid #e2e8f0;
    background: #fff;
    flex-shrink: 0;
}
#gaw-input-form { display: flex; gap: 8px; align-items: flex-end; }
#gaw-input {
    flex: 1; resize: none; border: 1px solid #e2e8f0; border-radius: 12px;
    padding: 10px 12px; font-size: 13px; max-height: 100px; min-height: 42px;
}
#gaw-input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
#gaw-send {
    background: linear-gradient(135deg, #7c3aed, #6366f1);
    border: none; color: #fff; border-radius: 12px;
    padding: 10px 14px; font-weight: 600; font-size: 13px; cursor: pointer; white-space: nowrap;
}
#gaw-send:disabled { opacity: 0.6; cursor: not-allowed; }

#gaw-overlay {
    position: fixed; inset: 0; background: rgba(15,23,42,0.35);
    z-index: 99988; opacity: 0; pointer-events: none; transition: opacity 0.3s;
}
#gaw-overlay.show { opacity: 1; pointer-events: auto; }

@media (max-width: 480px) {
    #gaw-panel { width: 100vw; }
    #gaw-toggle { right: 16px; bottom: 80px; width: 52px; height: 52px; }
}
</style>

<div id="gemini-admin-widget" aria-hidden="true">
    <div id="gaw-overlay"></div>

    <aside id="gaw-panel" role="dialog" aria-label="Gemini Admin Assistant">
        <div id="gaw-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="gaw-title-row">
                        <svg class="gaw-bot-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 8V4H8"/>
                            <rect x="4" y="8" width="16" height="12" rx="2"/>
                            <path d="M2 14h2"/>
                            <path d="M20 14h2"/>
                            <path d="M15 13v2"/>
                            <path d="M9 13v2"/>
                        </svg>
                        <h5 class="gaw-title-text">Gemini Assistant</h5>
                    </div>
                    <p class="gaw-title-text">ওয়েবসাইট ও অ্যাডমিন সম্পর্কে জিজ্ঞাসা করুন</p>
                </div>
                <button type="button" id="gaw-close-x" style="background:none;border:none;color:#fff;font-size:20px;cursor:pointer;line-height:1;padding:0;" aria-label="Close">✕</button>
            </div>
            <div id="gaw-header-actions">
                <button type="button" id="gaw-refresh">🔄 ডাটা রিফ্রেশ</button>
                <button type="button" id="gaw-clear">🗑️ ক্লিয়ার</button>
                <a href="{{ route('admin.gemini_chat.index') }}" target="_blank">↗ ফুল পেজ</a>
            </div>
        </div>

        <div id="gaw-messages"></div>
        <div id="gaw-typing"><i class="fe-loader"></i> ভাবছে...</div>

        <div id="gaw-input-wrap">
            <form id="gaw-input-form">
                <textarea id="gaw-input" rows="1" placeholder="প্রশ্ন লিখুন..." maxlength="4000"></textarea>
                <button type="submit" id="gaw-send">পাঠান</button>
            </form>
        </div>
    </aside>

    <button type="button" id="gaw-toggle" title="Gemini Assistant" aria-label="Open Gemini Assistant">
        <svg class="gaw-bot-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 8V4H8"/>
            <rect x="4" y="8" width="16" height="12" rx="2"/>
            <path d="M2 14h2"/>
            <path d="M20 14h2"/>
            <path d="M15 13v2"/>
            <path d="M9 13v2"/>
        </svg>
    </button>
</div>

<script>
(function () {
    var history = @json($geminiWidgetHistory);
    var routes = {
        send: @json(route('admin.gemini_chat.send')),
        clear: @json(route('admin.gemini_chat.clear')),
        refresh: @json(route('admin.gemini_chat.refresh_context')),
        csrf: @json(csrf_token())
    };

    var panel = document.getElementById('gaw-panel');
    var overlay = document.getElementById('gaw-overlay');
    var toggle = document.getElementById('gaw-toggle');
    var messages = document.getElementById('gaw-messages');
    var typing = document.getElementById('gaw-typing');
    var input = document.getElementById('gaw-input');
    var form = document.getElementById('gaw-input-form');
    var sendBtn = document.getElementById('gaw-send');
    var isOpen = false;
    var botIconSvg = '<svg class="gaw-bot-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 8V4H8"/><rect x="4" y="8" width="16" height="12" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>';

    function escapeHtml(t) {
        var d = document.createElement('div');
        d.textContent = t;
        return d.innerHTML;
    }

    function renderHistory() {
        messages.innerHTML = '';
        if (!history.length) {
            messages.innerHTML =
                '<div id="gaw-empty">' +
                '<div style="font-size:36px;margin-bottom:8px;">🤖</div>' +
                '<strong>কী জানতে চান?</strong><p class="mb-0 mt-1">অর্ডার, প্রোডাক্ট, সেটিংস — যেকোনো কিছু জিজ্ঞাসা করুন।</p>' +
                '<div id="gaw-suggestions">' +
                '<button type="button" data-q="আজ কতটি অর্ডার হয়েছে?">আজকের অর্ডার</button>' +
                '<button type="button" data-q="Pending product কিভাবে approve করব?">Product approve</button>' +
                '<button type="button" data-q="Fraud check কিভাবে করব?">Fraud check</button>' +
                '</div></div>';
            bindSuggestions();
            return;
        }
        history.forEach(function (m) {
            appendBubble(m.role === 'user' ? 'user' : 'bot', m.text, false);
        });
        scrollBottom();
    }

    function bindSuggestions() {
        messages.querySelectorAll('[data-q]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                sendMessage(btn.getAttribute('data-q'));
            });
        });
    }

    function appendBubble(role, text, scroll) {
        var empty = document.getElementById('gaw-empty');
        if (empty) empty.remove();

        var div = document.createElement('div');
        div.className = 'gaw-msg ' + (role === 'user' ? 'user' : 'bot');
        div.innerHTML =
            '<div class="gaw-avatar">' + (role === 'user' ? '<i class="fe-user"></i>' : botIconSvg) + '</div>' +
            '<div class="gaw-bubble">' + escapeHtml(text) + '</div>';
        messages.appendChild(div);
        if (role === 'user' && typeof feather !== 'undefined') feather.replace();
        if (scroll !== false) scrollBottom();
    }

    function scrollBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    function openPanel() {
        isOpen = true;
        panel.classList.add('open');
        overlay.classList.add('show');
        toggle.classList.add('open');
        toggle.innerHTML = '<i class="fe-x"></i>';
        document.getElementById('gemini-admin-widget').setAttribute('aria-hidden', 'false');
        setTimeout(function () { input.focus(); }, 300);
        if (typeof feather !== 'undefined') feather.replace();
    }

    function closePanel() {
        isOpen = false;
        panel.classList.remove('open');
        overlay.classList.remove('show');
        toggle.classList.remove('open');
        toggle.innerHTML = botIconSvg;
        document.getElementById('gemini-admin-widget').setAttribute('aria-hidden', 'true');
        if (typeof feather !== 'undefined') feather.replace();
    }

    function sendMessage(text) {
        text = (text || '').trim();
        if (!text) return;

        appendBubble('user', text);
        history.push({ role: 'user', text: text });
        input.value = '';
        input.style.height = 'auto';
        sendBtn.disabled = true;
        typing.classList.add('show');

        fetch(routes.send, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': routes.csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) {
            if (res.ok && res.data.success) {
                appendBubble('bot', res.data.reply);
                history.push({ role: 'model', text: res.data.reply });
            } else {
                var err = (res.data && res.data.message) ? res.data.message : 'ব্যর্থ হয়েছে';
                appendBubble('bot', '⚠️ ' + err);
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

    toggle.addEventListener('click', function () {
        isOpen ? closePanel() : openPanel();
    });
    document.getElementById('gaw-close-x').addEventListener('click', closePanel);
    overlay.addEventListener('click', closePanel);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        sendMessage(input.value);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit'));
        }
    });

    input.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    document.getElementById('gaw-clear').addEventListener('click', function () {
        if (!confirm('চ্যাট মুছবেন?')) return;
        fetch(routes.clear, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' }
        }).then(function () {
            history = [];
            renderHistory();
            if (typeof toastr !== 'undefined') toastr.success('চ্যাট ক্লিয়ার');
        });
    });

    document.getElementById('gaw-refresh').addEventListener('click', function () {
        fetch(routes.refresh, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': routes.csrf, 'Accept': 'application/json' }
        }).then(function () {
            if (typeof toastr !== 'undefined') toastr.success('সাইট ডাটা রিফ্রেশ হয়েছে');
        });
    });

    renderHistory();
    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
