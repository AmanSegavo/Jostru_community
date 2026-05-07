@extends('layouts.app')

@section('title', 'Video Call - ' . $receiver->name)

@section('content')
<div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #000; z-index: 9999;">
    
    <!-- Header Call -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; padding: 20px; background: linear-gradient(to bottom, rgba(0,0,0,0.85), transparent); z-index: 10; display: flex; align-items: center; justify-content: space-between;">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.2rem;">
                {{ substr($receiver->name, 0, 1) }}
            </div>
            <div>
                <h5 class="mb-0" style="color: white; font-weight: 700;">{{ $receiver->name }}</h5>
                <small style="color: #22c55e; font-weight: 600;">Panggilan Video Real-time • Jostru</small>
            </div>
        </div>

        <button onclick="endCall()" 
                class="btn btn-danger" 
                style="border-radius: 50px; padding: 10px 25px; font-weight: 700; border: none; box-shadow: 0 10px 20px rgba(220, 53, 69, 0.4);">
            <i class="fas fa-phone-slash mr-2"></i> Akhiri Panggilan
        </button>
    </div>

    <!-- Jitsi Container -->
    <div id="jitsi-container" style="width: 100%; height: 100%;"></div>

    <!-- Loading / Error Overlay (opsional) -->
    <div id="jitsi-status" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; text-align: center; display: none; z-index: 20;">
        <div class="spinner-border text-light mb-3" role="status"></div>
        <p id="jitsi-status-text">Menghubungkan ke video call...</p>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://meet.jit.si/external_api.js'></script>
<script>
    const domain = 'meet.jit.si';
    const options = {
        roomName: @json($roomName),
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-container'),
        userInfo: {
            displayName: @json(auth()->user()->name)
        },
        configOverwrite: { 
            startWithAudioMuted: false,
            startWithVideoMuted: false,
            disableThirdPartyRequests: true,
            prejoinPageEnabled: false,
            enableWelcomePage: false,
            enableClosePage: false
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'chat', 'settings',
                'videoquality', 'filmstrip', 'tileview', 'videobackgroundblur',
                'raisehand', 'stats'
            ],
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            DEFAULT_BACKGROUND: '#000000',
        }
    };

    let api = null;

    function initJitsi() {
        const statusEl = document.getElementById('jitsi-status');
        statusEl.style.display = 'block';

        try {
            api = new JitsiMeetExternalAPI(domain, options);

            // Event: Call started successfully
            api.addEventListener('videoConferenceJoined', () => {
                statusEl.style.display = 'none';
                console.log('%c[Jostru] Video call connected', 'color:#22c55e');
            });

            // Event: User left the call
            api.addEventListener('videoConferenceLeft', () => {
                window.location.href = "{{ route('member.chat.room', $receiver->id) }}";
            });

            // Event: Ready to close (tombol X di Jitsi)
            api.addEventListener('readyToClose', () => {
                window.location.href = "{{ route('member.chat.room', $receiver->id) }}";
            });

            // Event: Error handling
            api.addEventListener('error', (error) => {
                console.error('Jitsi Error:', error);
                statusEl.style.display = 'block';
                document.getElementById('jitsi-status-text').innerHTML = 
                    'Gagal terhubung ke video call.<br>Silakan coba lagi atau hubungi admin.';
            });

        } catch (e) {
            console.error('Failed to initialize Jitsi:', e);
            statusEl.style.display = 'block';
            document.getElementById('jitsi-status-text').innerHTML = 
                'Gagal memuat video call.<br>Periksa koneksi internet kamu.';
        }
    }

    // Fungsi untuk mengakhiri panggilan
    function endCall() {
        if (api) {
            api.executeCommand('hangup');
        }
        // Fallback redirect
        setTimeout(() => {
            window.location.href = "{{ route('member.chat.room', $receiver->id) }}";
        }, 800);
    }

    // Inisialisasi Jitsi
    window.onload = function() {
        initJitsi();
    };

    // Optional: Tekan ESC untuk akhiri call
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape" && api) {
            endCall();
        }
    });
</script>
@endpush