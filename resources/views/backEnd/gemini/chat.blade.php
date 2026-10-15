@extends('backEnd.layouts.master')
@section('title', 'Gemini Admin Assistant')

@section('css')
<style>
.gemini-chat-page {
    max-width: 960px;
    margin: 0 auto;
    padding: 8px 0 24px;
}
.gemini-chat-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 16px;
    padding: 18px 20px;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4c1d95 100%);
    border-radius: 16px;
    color: #fff;
    box-shadow: 0 12px 32px rgba(49, 46, 129, 0.35);
}
.gemini-chat-header h4 {
    margin: 0;
    font-weight: 700;
    font-size: 1.25rem;
}
.gemini-chat-header p {
    margin: 6px 0 0;
    font-size: 13px;
    opacity: 0.88;
    max-width: 520px;
}
.gemini-chat-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.gemini-chat-actions .btn {
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    padding: 8px 14px;
}
.gemini-chat-box {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
    display: flex;
    flex-direction: column;
    height: calc(100vh - 260px);
    min-height: 480px;
    overflow: hidden;
}
.gemini-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
}
.gemini-msg {
    display: flex;
    margin-bottom: 16px;
    gap: 10px;
    animation: fadeIn 0.25s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.gemini-msg.user { flex-direction: row-reverse; }
.gemini-msg-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}
.gemini-msg.user .gemini-msg-avatar {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
}
.gemini-msg.model .gemini-msg-avatar {
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    color: #fff;
}
.gemini-msg-bubble {
    max-width: 78%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.55;
    white-space: pre-wrap;
    word-break: break-word;
}
.gemini-msg.user .gemini-msg-bubble {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.gemini-msg.model .gemini-msg-bubble {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.gemini-msg-time {
    font-size: 10px;
    opacity: 0.65;
    margin-top: 4px;
}
.gemini-chat-empty {
    text-align: center;
    padding: 48px 20px;
    color: #64748b;
}
.gemini-chat-empty .icon {
    font-size: 48px;
    margin-bottom: 12px;
}
.gemini-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    margin-top: 16px;
}
.gemini-suggestion {
    border: 1px solid #c7d2fe;
    background: #eef2ff;
    color: #4338ca;
    border-radius: 999px;
    padding: 8px 14px;
    font-size: 12px;
    cursor: pointer;
    transition: background 0.2s;
}
.gemini-suggestion:hover {
    background: #e0e7ff;
}
.gemini-chat-input {
    border-top: 1px solid #e2e8f0;
    padding: 16px;
    background: #fff;
}
.gemini-chat-input form {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}
.gemini-chat-input textarea {
    flex: 1;
    resize: none;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    font-size: 14px;
    min-height: 48px;
    max-height: 140px;
}
.gemini-chat-input textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    outline: none;
}
.gemini-send-btn {
    background: linear-gradient(135deg, #7c3aed, #6366f1);
    border: none;
    color: #fff;
    border-radius: 12px;
    padding: 12px 20px;
    font-weight: 600;
    min-width: 100px;
}
.gemini-send-btn:disabled {
    opacity: 0.6;
}
.gemini-typing {
    display: none;
    padding: 0 20px 12px;
    font-size: 13px;
    color: #64748b;
}
.gemini-typing.show { display: block; }
@media (max-width: 768px) {
    .gemini-chat-box { height: calc(100vh - 220px); }
    .gemini-msg-bubble { max-width: 90%; }
}
</style>
@endsection

@section('content')
<div class="container-fluid gemini-chat-page">
    <div class="gemini-chat-header">
        <div>
            <h4>✨ Gemini Admin Assistant</h4>
            <p>ওয়েবসাইট, ডাটাবেস, অর্ডার, প্রোডাক্ট ও অ্যাডমিন প্যানেল সম্পর্কে যেকোনো প্রশ্ন করুন। বাংলা বা ইংরেজিতে কথা বলুন।</p>
        </div>
        <div class="gemini-chat-actions">
            <button type="button" class="btn btn-light btn-sm" id="btn-refresh-context">
                <i class="fe-refresh-cw"></i> Refresh Data
            </button>
            <button type="button" class="btn btn-outline-light btn-sm" id="btn-clear-chat">
                <i class="fe-trash-2"></i> Clear Chat
            </button>
            <a href="{{ route('admin.gemini_ai.edit') }}" class="btn btn-outline-light btn-sm">
                <i class="fe-settings"></i> API Settings
            </a>
        </div>
    </div>

    <div class="gemini-chat-box">
        <div class="gemini-chat-messages" id="chat-messages">
            @if(empty($history))
            <div class="gemini-chat-empty" id="chat-empty">
                <div class="icon">🤖</div>
                <h5 class="fw-bold text-dark">আজ কী সাহায্য লাগবে?</h5>
                <p class="mb-0 small">আমি আপনার স্টোরের ডাটা ও অ্যাডমিন সিস্টেম জানি।</p>
                <div class="gemini-suggestions">
                    <button type="button" class="gemini-suggestion" data-prompt="আজ কতটি অর্ডার হয়েছে?">আজ কত অর্ডার?</button>
                    <button type="button" class="gemini-suggestion" data-prompt="Pending product approve কিভাবে করব?">Product approve</button>
                    <button type="button" class="gemini-suggestion" data-prompt="Fraud check কিভাবে করব?">Fraud check</button>
                    <button type="button" class="gemini-suggestion" data-prompt="Gemini API key কোথায় সেট করব?">Gemini API setup</button>
                </div>
            </div>
            @else
                @foreach($history as $msg)
                <div class="gemini-msg {{ $msg['role'] === 'user' ? 'user' : 'model' }}">
                    <div class="gemini-msg-avatar">
                        <i class="fe-{{ $msg['role'] === 'user' ? 'user' : 'cpu' }}"></i>
                    </div>
                    <div>
                        <div class="gemini-msg-bubble">{{ $msg['text'] }}</div>
                        @if(!empty($msg['at']))
                        <div class="gemini-msg-time">{{ $msg['at'] }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>

        <div class="gemini-typing" id="chat-typing">
            <i class="fe-loader"></i> Gemini ভাবছে...
        </div>

        <div class="gemini-chat-input">
            <form id="chat-form">
                @csrf
                <textarea id="chat-input" rows="1" placeholder="মেসেজ লিখুন... (Enter = পাঠান, Shift+Enter = নতুন লাইন)" maxlength="4000"></textarea>
                <button type="submit" class="gemini-send-btn" id="chat-send-btn">
                    <i class="fe-send"></i> পাঠান
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    var $messages = $('#chat-messages');
    var $input = $('#chat-input');
    var $form = $('#chat-form');
    var $sendBtn = $('#chat-send-btn');
    var $typing = $('#chat-typing');

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function appendMessage(role, text, at) {
        $('#chat-empty').remove();
        var isUser = role === 'user';
        var html = '<div class="gemini-msg ' + (isUser ? 'user' : 'model') + '">' +
            '<div class="gemini-msg-avatar"><i class="fe-' + (isUser ? 'user' : 'cpu') + '"></i></div>' +
            '<div><div class="gemini-msg-bubble">' + escapeHtml(text) + '</div>' +
            (at ? '<div class="gemini-msg-time">' + escapeHtml(at) + '</div>' : '') +
            '</div></div>';
        $messages.append(html);
        $messages.scrollTop($messages[0].scrollHeight);
    }

    function sendMessage(text) {
        text = (text || '').trim();
        if (!text) return;

        appendMessage('user', text, new Date().toLocaleString('sv-SE', { hour12: false }).slice(0, 16).replace('T', ' '));
        $input.val('').css('height', 'auto');
        $sendBtn.prop('disabled', true);
        $typing.addClass('show');

        $.ajax({
            url: '{{ route('admin.gemini_chat.send') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                message: text
            },
            success: function (res) {
                if (res.success && res.reply) {
                    appendMessage('model', res.reply, new Date().toLocaleString('sv-SE', { hour12: false }).slice(0, 16).replace('T', ' '));
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(res.message || 'ব্যর্থ হয়েছে');
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'মেসেজ পাঠানো ব্যর্থ';
                if (typeof toastr !== 'undefined') toastr.error(msg);
                else alert(msg);
            },
            complete: function () {
                $sendBtn.prop('disabled', false);
                $typing.removeClass('show');
            }
        });
    }

    $form.on('submit', function (e) {
        e.preventDefault();
        sendMessage($input.val());
    });

    $input.on('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $form.trigger('submit');
        }
    });

    $input.on('input', function () {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 140) + 'px';
    });

    $('.gemini-suggestion').on('click', function () {
        sendMessage($(this).data('prompt'));
    });

    $('#btn-clear-chat').on('click', function () {
        if (!confirm('চ্যাট ইতিহাস মুছে ফেলবেন?')) return;
        $.post('{{ route('admin.gemini_chat.clear') }}', { _token: '{{ csrf_token() }}' }, function () {
            $messages.html(
                '<div class="gemini-chat-empty" id="chat-empty">' +
                '<div class="icon">🤖</div><h5 class="fw-bold text-dark">চ্যাট খালি</h5>' +
                '<p class="mb-0 small">নতুন প্রশ্ন করুন।</p></div>'
            );
            if (typeof toastr !== 'undefined') toastr.success('চ্যাট ক্লিয়ার হয়েছে');
        });
    });

    $('#btn-refresh-context').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.post('{{ route('admin.gemini_chat.refresh_context') }}', { _token: '{{ csrf_token() }}' }, function () {
            if (typeof toastr !== 'undefined') toastr.success('সাইট ডাটা রিফ্রেশ হয়েছে');
        }).always(function () { $btn.prop('disabled', false); });
    });

    $messages.scrollTop($messages[0].scrollHeight);
})();
</script>
@endsection
