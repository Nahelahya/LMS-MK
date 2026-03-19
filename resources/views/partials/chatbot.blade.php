{{-- resources/views/partials/chatbot.blade.php --}}
<div x-data="chatbotWidget()" style="position:fixed;bottom:24px;right:24px;z-index:2147483647;display:flex;flex-direction:column;align-items:flex-end;gap:12px;">

    <div x-show="open"
        style="width:340px;height:480px;display:none;flex-direction:column;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.5);border:1px solid rgba(99,102,241,0.25);background:#13131f;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#4f46e5;flex-shrink:0;">
            <div style="width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-robot" style="font-size:13px;color:white;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:13px;font-weight:600;color:white;line-height:1.3;">{{ __('messages.chatbot_title') }}</p>
                <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.7);line-height:1.3;">{{ __('messages.chatbot_subtitle') }}</p>
            </div>
            <button type="button" onclick="closeChatbot()" style="width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,0.15);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:white;">
                <i class="fas fa-times" style="font-size:12px;"></i>
            </button>
        </div>

        {{-- Messages --}}
        <div id="chatbot-msgbox" style="flex:1;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:10px;background:#0f0f1a;scrollbar-width:thin;scrollbar-color:#333 transparent;">
            <div id="chatbot-welcome" style="display:flex;gap:8px;align-items:flex-start;">
                <div style="width:28px;height:28px;border-radius:50%;background:#1e1e3a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-robot" style="font-size:11px;color:#818cf8;"></i>
                </div>
                <div style="background:#1a1a2e;border:1px solid rgba(99,102,241,0.2);border-radius:12px 12px 12px 2px;padding:10px 12px;font-size:13px;color:#c7c7d4;max-width:85%;line-height:1.5;">
                    @auth
                        @if(auth()->user()->role === 'student')
                            {{ __('messages.chatbot_welcome_student', ['name' => auth()->user()->name]) }}
                        @else
                            {{ __('messages.chatbot_welcome_staff', ['name' => auth()->user()->name]) }}
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        {{-- Suggested questions --}}
        <div id="chatbot-suggestions" style="padding:0 10px 8px;display:flex;flex-wrap:wrap;gap:6px;background:#0f0f1a;flex-shrink:0;">
            @auth
                @if(auth()->user()->role === 'student')
                    <button type="button" onclick="chatbotQuick('{{ __('messages.chatbot_q_student_1') }}')" style="font-size:11px;background:#1a1a2e;border:1px solid rgba(99,102,241,0.3);color:#818cf8;padding:4px 10px;border-radius:20px;cursor:pointer;">{{ __('messages.chatbot_q_student_1') }}</button>
                    <button type="button" onclick="chatbotQuick('{{ __('messages.chatbot_q_student_2') }}')" style="font-size:11px;background:#1a1a2e;border:1px solid rgba(99,102,241,0.3);color:#818cf8;padding:4px 10px;border-radius:20px;cursor:pointer;">{{ __('messages.chatbot_q_student_2') }}</button>
                    <button type="button" onclick="chatbotQuick('{{ __('messages.chatbot_q_student_3') }}')" style="font-size:11px;background:#1a1a2e;border:1px solid rgba(99,102,241,0.3);color:#818cf8;padding:4px 10px;border-radius:20px;cursor:pointer;">{{ __('messages.chatbot_q_student_3') }}</button>
                @else
                    <button type="button" onclick="chatbotQuick('{{ __('messages.chatbot_q_staff_1') }}')" style="font-size:11px;background:#1a1a2e;border:1px solid rgba(99,102,241,0.3);color:#818cf8;padding:4px 10px;border-radius:20px;cursor:pointer;">{{ __('messages.chatbot_q_staff_1') }}</button>
                    <button type="button" onclick="chatbotQuick('{{ __('messages.chatbot_q_staff_2') }}')" style="font-size:11px;background:#1a1a2e;border:1px solid rgba(99,102,241,0.3);color:#818cf8;padding:4px 10px;border-radius:20px;cursor:pointer;">{{ __('messages.chatbot_q_staff_2') }}</button>
                    <button type="button" onclick="chatbotQuick('{{ __('messages.chatbot_q_staff_3') }}')" style="font-size:11px;background:#1a1a2e;border:1px solid rgba(99,102,241,0.3);color:#818cf8;padding:4px 10px;border-radius:20px;cursor:pointer;">{{ __('messages.chatbot_q_staff_3') }}</button>
                @endif
            @endauth
        </div>

        {{-- Input --}}
        <div style="padding:10px 12px;background:#13131f;border-top:1px solid rgba(99,102,241,0.15);display:flex;gap:8px;align-items:flex-end;flex-shrink:0;">
            <textarea id="chatbot-input"
                placeholder="{{ __('messages.chatbot_placeholder') }}"
                rows="1"
                onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();chatbotSend();}"
                style="flex:1;resize:none;background:#0f0f1a;border:1px solid rgba(99,102,241,0.3);border-radius:10px;padding:8px 12px;font-size:13px;color:#e2e2ef;outline:none;min-height:38px;max-height:100px;font-family:inherit;line-height:1.4;"
                onfocus="this.style.borderColor='#6366f1'"
                onblur="this.style.borderColor='rgba(99,102,241,0.3)'"
            ></textarea>
            <button type="button" onclick="chatbotSend()" style="width:38px;height:38px;border-radius:10px;background:#4f46e5;border:none;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="fas fa-paper-plane" style="font-size:13px;"></i>
            </button>
        </div>
    </div>

    {{-- FAB --}}
    <button type="button" onclick="toggleChatbot()" id="chatbot-fab" style="width:52px;height:52px;border-radius:50%;background:#4f46e5;border:none;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(79,70,229,0.5);flex-shrink:0;">
        <i class="fas fa-robot" style="font-size:18px;"></i>
    </button>
</div>

<style>
@keyframes cb-bounce {
    0%,80%,100% { transform:translateY(0);opacity:.4; }
    40% { transform:translateY(-5px);opacity:1; }
}
</style>

<script>
// ── State ──────────────────────────────────────────────────────
var _cbOpen = false;
var _cbLoading = false;
var _cbUrl = '{{ route('chatbot.chat') }}';
var _cbCsrf = document.querySelector('meta[name=csrf-token]') ? document.querySelector('meta[name=csrf-token]').content : '';
var _cbErr = '{{ __('messages.chatbot_error') }}';

// ── Toggle open/close ──────────────────────────────────────────
function toggleChatbot() {
    _cbOpen = !_cbOpen;
    var win = document.querySelector('[x-data="chatbotWidget()"] > div:first-child');
    if (!win) win = document.querySelector('[x-data] > div[style*="340px"]');
    if (win) win.style.display = _cbOpen ? 'flex' : 'none';
    var fab = document.getElementById('chatbot-fab');
    if (fab) fab.innerHTML = _cbOpen
        ? '<i class="fas fa-times" style="font-size:18px;"></i>'
        : '<i class="fas fa-robot" style="font-size:18px;"></i>';
}

function closeChatbot() {
    _cbOpen = false;
    var win = document.querySelector('[x-data="chatbotWidget()"] > div:first-child');
    if (!win) win = document.querySelector('[x-data] > div[style*="340px"]');
    if (win) win.style.display = 'none';
    var fab = document.getElementById('chatbot-fab');
    if (fab) fab.innerHTML = '<i class="fas fa-robot" style="font-size:18px;"></i>';
}

// ── Send message ───────────────────────────────────────────────
function chatbotSend() {
    var input = document.getElementById('chatbot-input');
    var msg = input.value.trim();
    if (!msg || _cbLoading) return;

    chatbotAppend('user', msg);
    input.value = '';
    _cbLoading = true;

    // Sembunyikan suggestions
    var sug = document.getElementById('chatbot-suggestions');
    if (sug) sug.style.display = 'none';
    var welcome = document.getElementById('chatbot-welcome');
    if (welcome) welcome.style.display = 'none';

    // Loading dots
    var loadId = 'cb-load-' + Date.now();
    chatbotAppendLoading(loadId);

    fetch(_cbUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': _cbCsrf,
        },
        body: JSON.stringify({ message: msg }),
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        document.getElementById(loadId).remove();
        chatbotAppend('bot', data.reply || _cbErr);
        _cbLoading = false;
    })
    .catch(function() {
        document.getElementById(loadId).remove();
        chatbotAppend('bot', _cbErr);
        _cbLoading = false;
    });
}

function chatbotQuick(msg) {
    var input = document.getElementById('chatbot-input');
    input.value = msg;
    chatbotSend();
}

// ── Append message bubble ──────────────────────────────────────
function chatbotAppend(role, text) {
    var box = document.getElementById('chatbot-msgbox');
    var wrap = document.createElement('div');

    if (role === 'user') {
        wrap.style.cssText = 'display:flex;justify-content:flex-end;';
        var bubble = document.createElement('div');
        bubble.style.cssText = 'background:#4f46e5;color:white;border-radius:12px 12px 2px 12px;padding:9px 12px;font-size:13px;max-width:85%;line-height:1.5;word-break:break-word;white-space:pre-wrap;';
        bubble.textContent = text;
        wrap.appendChild(bubble);
    } else {
        wrap.style.cssText = 'display:flex;gap:8px;align-items:flex-start;';
        var avatar = document.createElement('div');
        avatar.style.cssText = 'width:28px;height:28px;border-radius:50%;background:#1e1e3a;display:flex;align-items:center;justify-content:center;flex-shrink:0;';
        avatar.innerHTML = '<i class="fas fa-robot" style="font-size:11px;color:#818cf8;"></i>';
        var bubble = document.createElement('div');
        bubble.style.cssText = 'background:#1a1a2e;border:1px solid rgba(99,102,241,0.2);color:#c7c7d4;border-radius:12px 12px 12px 2px;padding:9px 12px;font-size:13px;max-width:85%;line-height:1.5;word-break:break-word;white-space:pre-wrap;';
        bubble.textContent = text;
        wrap.appendChild(avatar);
        wrap.appendChild(bubble);
    }

    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

function chatbotAppendLoading(id) {
    var box = document.getElementById('chatbot-msgbox');
    var wrap = document.createElement('div');
    wrap.id = id;
    wrap.style.cssText = 'display:flex;gap:8px;align-items:flex-start;';
    wrap.innerHTML = '<div style="width:28px;height:28px;border-radius:50%;background:#1e1e3a;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-robot" style="font-size:11px;color:#818cf8;"></i></div>'
        + '<div style="background:#1a1a2e;border:1px solid rgba(99,102,241,0.2);border-radius:12px 12px 12px 2px;padding:12px 14px;">'
        + '<div style="display:flex;gap:4px;align-items:center;">'
        + '<span style="width:6px;height:6px;background:#818cf8;border-radius:50%;display:inline-block;animation:cb-bounce 1s infinite 0ms;"></span>'
        + '<span style="width:6px;height:6px;background:#818cf8;border-radius:50%;display:inline-block;animation:cb-bounce 1s infinite 150ms;"></span>'
        + '<span style="width:6px;height:6px;background:#818cf8;border-radius:50%;display:inline-block;animation:cb-bounce 1s infinite 300ms;"></span>'
        + '</div></div>';
    box.appendChild(wrap);
    box.scrollTop = box.scrollHeight;
}

// ── Dummy Alpine component (tetap diperlukan karena x-data) ───
function chatbotWidget() { return {}; }
</script>