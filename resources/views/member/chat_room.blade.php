@extends('layouts.app')
@section('title', 'Chat - ' . $receiver->name)

@section('content')
<!-- Include Emoji Picker -->
<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1.18.2/index.js"></script>

<div class="container mt-4 animate-fade-in chat-container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <!-- Header -->
            <div class="card glass mb-3 p-3 d-flex flex-row align-items-center justify-content-between" style="border-radius:24px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: relative; z-index: 10;">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('member.chat.list') }}" class="btn btn-light rounded-circle p-2" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="position-relative">
                        <div style="width:50px;height:50px;background:linear-gradient(135deg, #3b82f6, #2563eb);border-radius:18px;display:flex;align-items:center;justify-content:center;color:white;font-weight:800; font-size: 1.2rem; box-shadow: 0 8px 15px rgba(59,130,246,0.3);">
                            {{ strtoupper(substr($receiver->name, 0, 1)) }}
                        </div>
                        <span class="status-indicator {{ $receiver->is_online ? 'online' : 'offline' }}"></span>
                    </div>
                    <div>
                        <h5 class="mb-0" style="font-weight:800; color: #1e293b;">{{ $receiver->name }}</h5>
                        <small class="{{ $receiver->is_online ? 'text-success' : 'text-muted' }}" style="font-weight:600;">
                            {{ $receiver->is_online ? 'Sedang Online' : 'Offline' }}
                        </small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('member.video_call', $receiver->id) }}" class="btn action-btn bg-success-subtle text-success">
                        <i class="fas fa-video"></i>
                    </a>
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div class="card glass p-4 mb-3 chat-messages-area" id="chat-messages">
                @if($messages->isEmpty())
                    <div class="m-auto text-center text-muted w-100" id="empty-state">
                        <div style="font-size: 4rem; margin-bottom: 1rem; filter: grayscale(1); opacity: 0.2;">👋</div>
                        <p class="mb-0" style="font-weight:600;">Mulai obrolan seru dengan <strong>{{ $receiver->name }}</strong></p>
                    </div>
                @endif
                
                @foreach($messages as $msg)
                <div class="message-wrapper {{ $msg->sender_id == auth()->id() ? 'mine' : 'theirs' }}">
                    <div class="chat-bubble">
                        @if($msg->attachment_path)
                            <div class="mb-2 attachment-preview">
                                @if($msg->attachment_type === 'image')
                                    <img src="{{ asset($msg->attachment_path) }}" alt="attachment" class="img-fluid rounded" style="max-height: 250px; object-fit: cover;">
                                @elseif($msg->attachment_type === 'video')
                                    <video src="{{ asset($msg->attachment_path) }}" controls class="img-fluid rounded" style="max-height: 250px;"></video>
                                @endif
                            </div>
                        @endif
                        
                        <div class="message-text">
                            {!! preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="chat-link">$1</a>', htmlspecialchars($msg->message ?? '')) !!}
                        </div>
                        <div class="chat-time">{{ $msg->created_at->format('H:i') }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- File Preview Area (Hidden by default) -->
            <div id="file-preview-container" class="card glass p-3 mb-2" style="display:none; border-radius: 16px;">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-image text-primary fs-4"></i>
                        <span id="file-preview-name" class="fw-bold text-truncate" style="max-width:200px;"></span>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger rounded-circle" id="remove-file-btn"><i class="fas fa-times"></i></button>
                </div>
            </div>

            <!-- Input Form -->
            <div class="card glass p-3" style="border-radius:24px; position: relative;">
                <!-- Emoji Picker Popup -->
                <div id="emoji-popup" class="emoji-popup" style="display:none;">
                    <emoji-picker class="light"></emoji-picker>
                </div>
                
                <form id="chat-form" class="d-flex gap-2 align-items-end">
                    <input type="file" id="attachment-input" style="display:none;" accept="image/*,video/*">
                    <button type="button" class="btn btn-light icon-btn rounded-circle" id="attach-btn" title="Kirim Media">
                        <i class="fas fa-paperclip text-muted"></i>
                    </button>
                    
                    <button type="button" class="btn btn-light icon-btn rounded-circle" id="emoji-btn" title="Pilih Emoji">
                        <i class="far fa-smile text-warning"></i>
                    </button>

                    <div class="flex-grow-1 position-relative">
                        <textarea id="message-input" class="form-control" placeholder="Ketik pesan..." rows="1" style="border-radius:20px; resize:none; padding:12px 18px; border:2px solid #e2e8f0;"></textarea>
                    </div>
                    
                    <button type="submit" id="send-btn" class="btn btn-primary rounded-circle send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Status Indicator */
    .status-indicator { position: absolute; bottom: -2px; right: -2px; width: 14px; height: 14px; border: 3px solid white; border-radius: 50%; }
    .status-indicator.online { background: #22c55e; } .status-indicator.offline { background: #94a3b8; }

    /* Layout & Buttons */
    .action-btn { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
    .action-btn:hover { transform: translateY(-2px); }
    .icon-btn { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: background 0.2s; border: none; }
    .icon-btn:hover { background: #f1f5f9; }
    .send-btn { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; box-shadow: 0 4px 15px rgba(59,130,246,0.3); transition: transform 0.2s; }
    .send-btn:active { transform: scale(0.9); }

    /* Chat Area */
    .chat-messages-area { height: 65vh; min-height: 400px; overflow-y: auto; display: flex; flex-direction: column; background: #f8fafc; border: 1px solid rgba(0,0,0,0.05); }
    .chat-messages-area::-webkit-scrollbar { width: 6px; } .chat-messages-area::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
    
    /* Premium Chat Bubbles */
    .message-wrapper { display: flex; margin-bottom: 1rem; animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    .message-wrapper.mine { justify-content: flex-end; }
    .message-wrapper.theirs { justify-content: flex-start; }

    .chat-bubble { max-width: 75%; padding: 14px 18px; border-radius: 20px; font-size: 0.95rem; line-height: 1.5; position: relative; word-wrap: break-word; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    .mine .chat-bubble { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-bottom-right-radius: 4px; }
    .theirs .chat-bubble { background: white; color: #1e293b; border-bottom-left-radius: 4px; border: 1px solid rgba(0,0,0,0.03); }

    .chat-time { font-size: 0.65rem; margin-top: 6px; display: block; text-align: right; }
    .mine .chat-time { color: rgba(255,255,255,0.7); } .theirs .chat-time { color: #94a3b8; }
    
    .chat-link { text-decoration: underline; font-weight: 600; }
    .mine .chat-link { color: #f8fafc; } .theirs .chat-link { color: #2563eb; }

    @keyframes slideUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

    /* Emoji Popup */
    .emoji-popup { position: absolute; bottom: 85px; left: 20px; z-index: 1000; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border-radius: 16px; overflow: hidden; animation: popIn 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes popIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
</style>
@endsection

@push('scripts')
<script>
const chatContainer = document.getElementById('chat-messages');
const form = document.getElementById('chat-form');
const input = document.getElementById('message-input');
const emptyState = document.getElementById('empty-state');
const emojiBtn = document.getElementById('emoji-btn');
const emojiPopup = document.getElementById('emoji-popup');
const attachBtn = document.getElementById('attach-btn');
const attachmentInput = document.getElementById('attachment-input');
const filePreviewContainer = document.getElementById('file-preview-container');
const filePreviewName = document.getElementById('file-preview-name');
const removeFileBtn = document.getElementById('remove-file-btn');

let lastMessageId = {{ $messages->last()->id ?? 0 }};
let isTabActive = true;
let selectedFile = null;

// Tab visibility detection untuk cegah lag saat tab background
document.addEventListener("visibilitychange", () => {
    isTabActive = !document.hidden;
});

// Scroll to bottom
function scrollToBottom() {
    chatContainer.scrollTop = chatContainer.scrollHeight;
}
scrollToBottom();

// Auto-resize textarea
input.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight < 100 ? this.scrollHeight : 100) + 'px';
});
input.addEventListener('keydown', function(e) {
    if(e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        form.dispatchEvent(new Event('submit'));
    }
});

// Emoji Picker
document.querySelector('emoji-picker').addEventListener('emoji-click', event => {
    input.value += event.detail.unicode;
    input.focus();
});
emojiBtn.addEventListener('click', () => {
    emojiPopup.style.display = emojiPopup.style.display === 'none' ? 'block' : 'none';
});
document.addEventListener('click', (e) => {
    if(!emojiBtn.contains(e.target) && !emojiPopup.contains(e.target)) {
        emojiPopup.style.display = 'none';
    }
});

// Attachment handling
attachBtn.addEventListener('click', () => attachmentInput.click());
attachmentInput.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        selectedFile = this.files[0];
        filePreviewName.textContent = selectedFile.name;
        filePreviewContainer.style.display = 'block';
    }
});
removeFileBtn.addEventListener('click', () => {
    selectedFile = null;
    attachmentInput.value = '';
    filePreviewContainer.style.display = 'none';
});

// Format URL to Link
function parseUrls(text) {
    if(!text) return '';
    const urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(urlRegex, '<a href="$1" target="_blank" class="chat-link">$1</a>');
}

// Render message HTML
function renderMessage(msg, isMine) {
    const time = new Date(msg.created_at || new Date()).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    let attachmentHtml = '';
    
    if (msg.attachment_path) {
        if (msg.attachment_type === 'image') {
            attachmentHtml = `<div class="mb-2 attachment-preview"><img src="${msg.attachment_path}" class="img-fluid rounded" style="max-height: 250px; object-fit: cover;"></div>`;
        } else if (msg.attachment_type === 'video') {
            attachmentHtml = `<div class="mb-2 attachment-preview"><video src="${msg.attachment_path}" controls class="img-fluid rounded" style="max-height: 250px;"></video></div>`;
        }
    }
    
    return `
        <div class="message-wrapper ${isMine ? 'mine' : 'theirs'}">
            <div class="chat-bubble">
                ${attachmentHtml}
                <div class="message-text">${parseUrls(msg.message)}</div>
                <div class="chat-time">${time}</div>
            </div>
        </div>
    `;
}

// Submit Form
form.addEventListener('submit', function(e) {
    e.preventDefault();
    const messageText = input.value.trim();
    if (!messageText && !selectedFile) return;

    if (emptyState) emptyState.remove();
    emojiPopup.style.display = 'none';

    // Optimistic UI (khusus text)
    let tempMsgElement = null;
    if (messageText && !selectedFile) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = renderMessage({ message: messageText }, true);
        tempMsgElement = tempDiv.firstElementChild;
        tempMsgElement.style.opacity = '0.6';
        chatContainer.appendChild(tempMsgElement);
        scrollToBottom();
    }
    
    input.value = '';
    input.style.height = 'auto';
    
    // Siapkan FormData
    const formData = new FormData();
    if (messageText) formData.append('message', messageText);
    if (selectedFile) formData.append('attachment', selectedFile);
    formData.append('_token', '{{ csrf_token() }}');

    // Reset UI File
    selectedFile = null;
    attachmentInput.value = '';
    filePreviewContainer.style.display = 'none';

    fetch("{{ route('member.chat.send', $receiver->id) }}", {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (tempMsgElement) tempMsgElement.remove(); // Hapus yg dummy
            // Append real dari server
            chatContainer.insertAdjacentHTML('beforeend', renderMessage(data, true));
            scrollToBottom();
            if (data.id > lastMessageId) lastMessageId = data.id;
        } else {
            if (tempMsgElement) tempMsgElement.style.border = '1px solid red';
            alert('Gagal mengirim: ' + (data.error || 'Server error'));
        }
    })
    .catch(() => {
        if (tempMsgElement) tempMsgElement.style.border = '1px solid red';
    });
});

// Optimized Polling (Pause if tab is hidden)
setInterval(() => {
    if (!isTabActive) return; // Prevent lag
    
    fetch("{{ route('member.chat.poll', $receiver->id) }}?after=" + lastMessageId)
        .then(res => res.json())
        .then(messages => {
            if (messages && messages.length > 0) {
                if (emptyState) emptyState.remove();
                messages.forEach(msg => {
                    if (msg.id > lastMessageId) {
                        lastMessageId = msg.id;
                        const isMine = msg.sender_id == {{ auth()->id() }};
                        chatContainer.insertAdjacentHTML('beforeend', renderMessage(msg, isMine));
                    }
                });
                scrollToBottom();
            }
        }).catch(() => {});
}, 3500); 
</script>
@endpush