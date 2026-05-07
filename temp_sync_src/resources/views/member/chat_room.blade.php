@extends('layouts.app')
@section('title', 'Chat - ' . $receiver->name)

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header -->
            <div class="card glass mb-3 p-3 d-flex flex-row align-items-center justify-content-between" style="border-radius:20px;">
                <div class="d-flex align-items-center">
                    <a href="{{ route('member.chat.list') }}" class="btn btn-link p-0 me-3 text-muted">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div style="width:45px;height:45px;background:#22c55e;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;margin-right:15px;">
                        {{ substr($receiver->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0" style="font-weight:700;">{{ $receiver->name }}</h6>
                        <small class="text-success" style="font-weight:600;">Aktif Sekarang</small>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('member.video_call', $receiver->id) }}" class="btn btn-primary" style="border-radius:50%;width:45px;height:45px;display:flex;align-items:center;justify-content:center;background:#22c55e;border:none;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v3a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v3a1 1 0 0 1 1 1H1a1 1 0 0 1-1-1V1z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="card glass p-4 mb-3" style="height:500px;overflow-y:auto;border-radius:24px;" id="chat-messages">
                @foreach($messages as $msg)
                <div class="mb-3 d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                    <div style="max-width:75%;padding:12px 20px;border-radius:18px;{{ $msg->sender_id == auth()->id() ? 'background:#22c55e;color:white;border-bottom-right-radius:4px;' : 'background:rgba(var(--primary-rgb),0.05);color:var(--text-color);border-bottom-left-radius:4px;' }}">
                        {{ $msg->message }}
                        <small class="d-block mt-1 {{ $msg->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size:0.65rem;">
                            {{ $msg->created_at->format('H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Input -->
            <div class="card glass p-3" style="border-radius:20px;">
                <form id="chat-form" class="d-flex gap-2">
                    <input type="text" id="message-input" class="form-control" placeholder="Tulis pesan..." style="border-radius:12px;border:1px solid rgba(0,0,0,0.05);padding:12px 20px;">
                    <button type="submit" class="btn btn-primary" style="width:50px;height:50px;border-radius:12px;background:#22c55e;border:none;display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" fill="white" viewBox="0 0 16 16"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11z"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chatContainer = document.getElementById('chat-messages');
const form = document.getElementById('chat-form');
const input = document.getElementById('message-input');
let lastMessageId = {{ $messages->last()->id ?? 0 }};

chatContainer.scrollTop = chatContainer.scrollHeight;

// Kirim pesan
form.addEventListener('submit', function(e) {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    // Optimistic UI
    const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    const msgHtml = `
        <div class="mb-3 d-flex justify-content-end">
            <div style="max-width:75%;padding:12px 20px;border-radius:18px;background:#22c55e;color:white;border-bottom-right-radius:4px;">
                ${message}
                <small class="d-block mt-1 text-white-50" style="font-size:0.65rem;">${time}</small>
            </div>
        </div>
    `;
    chatContainer.insertAdjacentHTML('beforeend', msgHtml);
    chatContainer.scrollTop = chatContainer.scrollHeight;
    input.value = '';

    // Kirim ke server
    fetch("{{ route('member.chat.send', $receiver->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: message })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert('Gagal mengirim pesan');
        }
    })
    .catch(() => {
        // Bisa tambahkan retry logic di sini
    });
});

// Polling untuk pesan baru (setiap 4 detik)
setInterval(() => {
    fetch("{{ route('member.chat.poll', $receiver->id) }}?after=" + lastMessageId)
        .then(res => res.json())
        .then(messages => {
            if (messages.length > 0) {
                messages.forEach(msg => {
                    const isMine = msg.sender_id == {{ auth()->id() }};
                    const time = new Date(msg.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                    
                    const html = `
                        <div class="mb-3 d-flex ${isMine ? 'justify-content-end' : 'justify-content-start'}">
                            <div style="max-width:75%;padding:12px 20px;border-radius:18px;${isMine ? 'background:#22c55e;color:white;border-bottom-right-radius:4px;' : 'background:rgba(var(--primary-rgb),0.05);color:var(--text-color);border-bottom-left-radius:4px;'}">
                                ${msg.message}
                                <small class="d-block mt-1 ${isMine ? 'text-white-50' : 'text-muted'}" style="font-size:0.65rem;">${time}</small>
                            </div>
                        </div>
                    `;
                    chatContainer.insertAdjacentHTML('beforeend', html);
                    lastMessageId = msg.id;
                });
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        })
        .catch(() => {});
}, 4000);
</script>
@endpush