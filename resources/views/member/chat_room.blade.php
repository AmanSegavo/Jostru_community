@extends('layouts.app')

@section('title', 'Chat - ' . $receiver->name)

@section('content')
<div class="container mt-4 animate-fade-in">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Chat Header -->
            <div class="card glass mb-3 p-3 d-flex flex-row align-items-center justify-content-between" style="border-radius: 20px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
                <div class="d-flex align-items-center">
                    <a href="{{ route('member.chat.list') }}" class="btn btn-link p-0 me-3 text-muted">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <div style="width: 45px; height: 45px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; margin-right: 15px;">
                        {{ substr($receiver->name, 0, 1) }}
                    </div>
                    <div>
                        <h6 class="mb-0" style="font-weight: 700;">{{ $receiver->name }}</h6>
                        <small class="text-success" style="font-weight: 600;">Aktif Sekarang</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('member.video_call', $receiver->id) }}?audioOnly=true" class="btn btn-light" style="border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.1);">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5z"/></svg>
                    </a>
                    <a href="{{ route('member.video_call', $receiver->id) }}" class="btn btn-primary" style="border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; background: #22c55e; border: none; box-shadow: 0 5px 15px rgba(34, 197, 94, 0.3);">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v3a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v3a1 1 0 0 1 1 1H1a1 1 0 0 1-1-1V1z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="card glass p-4 mb-3" style="height: 500px; overflow-y: auto; border-radius: 24px; border: 1px solid rgba(var(--primary-rgb), 0.1);" id="chat-messages">
                @foreach($messages as $msg)
                <div class="mb-3 d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                    <div style="max-width: 75%; padding: 12px 20px; border-radius: 18px; position: relative; {{ $msg->sender_id == auth()->id() ? 'background: #22c55e; color: white; border-bottom-right-radius: 4px;' : 'background: rgba(var(--primary-rgb), 0.05); color: var(--text-color); border-bottom-left-radius: 4px;' }}">
                        {{ $msg->message }}
                        <small class="d-block mt-1 {{ $msg->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.65rem;">
                            {{ $msg->created_at->format('H:i') }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Input Area -->
            <div class="card glass p-3" style="border-radius: 20px; border: 1px solid rgba(var(--primary-rgb), 0.1);">
                <form id="chat-form" class="d-flex gap-2">
                    <input type="text" id="message-input" class="form-control" placeholder="Tulis pesan..." style="border-radius: 12px; border: 1px solid rgba(0,0,0,0.05); padding: 12px 20px;">
                    <button type="submit" class="btn btn-primary" style="width: 50px; height: 50px; border-radius: 12px; background: #22c55e; border: none; display: flex; align-items: center; justify-content: center;">
                        <svg width="20" height="20" fill="white" viewBox="0 0 16 16"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11zM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493z"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const chatContainer = document.getElementById('chat-messages');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        if (!message) return;

        // Optimistic UI
        const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const msgHtml = `
            <div class="mb-3 d-flex justify-content-end">
                <div style="max-width: 75%; padding: 12px 20px; border-radius: 18px; background: #22c55e; color: white; border-bottom-right-radius: 4px;">
                    ${message}
                    <small class="d-block mt-1 text-white-50" style="font-size: 0.65rem;">${time}</small>
                </div>
            </div>
        `;
        chatContainer.insertAdjacentHTML('beforeend', msgHtml);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        input.value = '';

        // Send to server
        fetch("{{ route('member.chat.send', $receiver->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        });
    });

    // Auto-refresh (Simulasi real-time tanpa Pusher di Shared Hosting)
    setInterval(() => {
        // Hanya refresh jika tidak sedang fokus mengetik
        // fetch data terbaru... (bisa diimplementasi untuk real-time murni)
    }, 5000);
</script>
@endpush
@endsection
