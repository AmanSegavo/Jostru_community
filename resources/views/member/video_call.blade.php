@extends('layouts.app')

@section('title', 'Video Call - ' . $receiver->name)

@section('content')
<div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #000; z-index: 9999;">
    <!-- Header Call -->
    <div style="position: absolute; top: 0; left: 0; width: 100%; padding: 20px; background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent); z-index: 10; display: flex; align-items: center; justify-content: space-between;">
        <div class="d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: #22c55e; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.2rem;">
                {{ substr($receiver->name, 0, 1) }}
            </div>
            <div>
                <h5 class="mb-0" style="color: white; font-weight: 700;">{{ $receiver->name }}</h5>
                <small style="color: #22c55e; font-weight: 600;">Panggilan Video Real-time</small>
            </div>
        </div>
        <a href="{{ route('member.chat.room', $receiver->id) }}" class="btn btn-danger" style="border-radius: 50px; padding: 10px 25px; font-weight: 700; border: none; box-shadow: 0 10px 20px rgba(220, 53, 69, 0.4);">
            Akhiri Panggilan
        </a>
    </div>

    <!-- Jitsi Container -->
    <div id="jitsi-container" style="width: 100%; height: 100%;"></div>
</div>

@push('scripts')
<script src='https://meet.jit.si/external_api.js'></script>
<script>
    const domain = 'meet.jit.si';
    const options = {
        roomName: '{{ $roomName }}',
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#jitsi-container'),
        userInfo: {
            displayName: '{{ auth()->user()->name }}'
        },
        configOverwrite: { 
            startWithAudioMuted: false,
            disableThirdPartyRequests: true,
            prejoinPageEnabled: false
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: [
                'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                'fodeviceselection', 'hangup', 'profile', 'chat', 'recording',
                'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                'security'
            ],
        }
    };
    const api = new JitsiMeetExternalAPI(domain, options);
    
    // Auto redirect when call ends
    api.addEventListener('videoConferenceLeft', () => {
        window.location.href = "{{ route('member.chat.room', $receiver->id) }}";
    });
</script>
@endpush
@endsection
