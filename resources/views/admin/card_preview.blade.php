@extends('layouts.admin')
@section('admin_content')
    <style>
        @keyframes tilt-phone {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(-90deg); }
            100% { transform: rotate(0deg); }
        }

        #landscape-warning {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 2rem;
            backdrop-filter: blur(15px);
        }

        .phone-icon {
            width: 80px;
            height: 140px;
            border: 4px solid #22c55e;
            border-radius: 12px;
            margin-bottom: 2rem;
            position: relative;
            animation: tilt-phone 3s ease-in-out infinite;
            box-shadow: 0 0 30px rgba(34, 197, 94, 0.3);
        }

        .phone-icon::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 15px;
            height: 15px;
            border: 2px solid #22c55e;
            border-radius: 50%;
        }

        @media (max-width: 991px) and (orientation: portrait) {
            #landscape-warning {
                display: flex;
            }
        }

        .editor-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }

        .pulse-animation {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* Responsive Canvas Container */
        #canvas-wrapper {
            width: 100%;
            max-width: 874px; /* Desktop standard */
            margin: 0 auto;
            overflow: hidden;
            border: 12px solid #fff;
            border-radius: 28px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.2);
            background: #fff;
            position: relative;
            /* Height will be set by JS */
        }

        #card-canvas {
            position: absolute;
            left: 0;
            top: 0;
            width: 1748px;
            height: 1240px;
            background: url('{{ asset('images/template_kartu.png') }}') no-repeat;
            background-size: cover;
            transform-origin: top left;
        }
    </style>

    <div id="landscape-warning">
        <div class="phone-icon"></div>
        <h2 style="font-weight: 800; color: #22c55e;">Miringkan Layar Anda</h2>
        <p class="mt-3" style="font-size: 1.1rem; opacity: 0.8; max-width: 400px;">Halaman ini lebih optimal jika HP dalam posisi miring (landscape).</p>
        <button onclick="document.getElementById('landscape-warning').style.display='none'" class="btn btn-outline-light mt-4" style="border-radius: 30px; padding: 10px 25px;">Tetap Lanjutkan</button>
    </div>

    <div class="animate-fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <div class="editor-badge">
                    <span class="pulse-animation"></span>
                    MODE PREVIEW & EDITOR AKTIF
                </div>
                <h2 class="mb-0" style="font-weight: 800;">Digital ID Card Designer</h2>
            </div>
            <a href="{{ route('admin.members') }}" class="btn btn-outline" style="border-radius: 30px; padding: 10px 20px;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right: 8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>
        
        <div class="card p-0 glass text-center" style="overflow: hidden; border: 1px solid rgba(var(--primary-rgb), 0.2); border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.15);">
            <div style="background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.07), rgba(var(--secondary-rgb), 0.07)); padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h4 class="mb-0" style="color: var(--primary); font-weight: 800;">Editor: {{ $user->name }}</h4>
                <p class="text-muted mt-2" style="font-size: 14px; margin: 0;">Sentuh dan geser teks. Canvas akan otomatis menyesuaikan ukuran layar HP Anda.</p>
            </div>
            
            <div style="padding: 2rem 1rem; background: radial-gradient(circle at center, rgba(var(--primary-rgb), 0.05), transparent);">
                <div id="canvas-wrapper">
                    <div id="card-canvas">
                        
                        <div id="drag-nama" class="draggable-text" style="position: absolute; left: 530px; top: 460px; font-size: 60px; font-weight: bold; font-family: 'Inter', Arial, sans-serif; color: white; cursor: move; white-space: normal; max-width: 850px; line-height: 1.15; user-select: none; text-shadow: 3px 3px 8px rgba(0,0,0,0.5); touch-action: none;">
                            NAMA : <span contenteditable="true" id="input-nama" style="outline: 2px dashed rgba(255,255,255,0.7); padding: 5px 20px; cursor: text; word-wrap: break-word; border-radius: 12px; background: rgba(0,0,0,0.2);">{{ strtoupper($user->name) }}</span>
                        </div>

                        <div id="drag-id" class="draggable-text" style="position: absolute; left: 530px; top: 560px; font-size: 60px; font-family: 'Inter', Arial, sans-serif; color: white; cursor: move; white-space: nowrap; user-select: none; text-shadow: 3px 3px 8px rgba(0,0,0,0.5); touch-action: none;">
                            ID : {{ $user->member_id ?? 'JC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                        </div>

                        <div id="drag-status" class="draggable-text" style="position: absolute; left: 530px; top: 660px; font-size: 60px; font-family: 'Inter', Arial, sans-serif; color: white; cursor: move; white-space: nowrap; user-select: none; text-shadow: 3px 3px 8px rgba(0,0,0,0.5); touch-action: none;">
                            STATUS : 
                            <select id="input-status" style="background: rgba(0,0,0,0.2); color: white; border: 2px dashed rgba(255,255,255,0.7); border-radius: 12px; padding: 0 20px; font-size: inherit; font-family: inherit; cursor: pointer; outline: none; font-weight: bold;">
                                <option style="color: black;" value="AKTIF" {{ ($user->status ?? 'AKTIF') === 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                                <option style="color: black;" value="TIDAK AKTIF" {{ ($user->status ?? 'AKTIF') === 'TIDAK AKTIF' ? 'selected' : '' }}>TIDAK AKTIF</option>
                            </select>
                        </div>

                        <div id="qr-box" style="position: absolute; left: 1114px; top: 720px; width: 345px; height: 345px; background: rgba(255,255,255,0.98); border: 4px dashed rgba(0,0,0,0.1); border-radius: 24px; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 0 40px rgba(0,0,0,0.05);">
                            <div style="text-align: center;">
                                <svg width="100" height="100" fill="none" stroke="rgba(0,0,0,0.2)" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-1.5m0 0H8m-2.5 0H4m8-4v1m0 0v1.5m0 0V19m0-1.5H8m4 0h4m-8-4H4m4 0v-1m0 0v-1.5m0 0V8m0 1.5H4m8-4h4m0 0v1.5m0 0V8m0-1.5h2m-6 0h-1.5M16 12h-1.5m0 0H12m4 0v1.5m0 0V16m0-1.5h2"></path></svg>
                                <div style="font-size: 38px; font-weight: 900; color: rgba(0,0,0,0.3); font-family: 'Inter', sans-serif; letter-spacing: 3px;">LIVE QR</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="padding: 2rem; background: var(--surface-color); border-top: 1px solid var(--border-color);">
                <form id="editor-form" action="{{ route('admin.generate_card_custom', $user->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="nama_text" id="val-nama" value="">
                    <input type="hidden" name="status_text" id="val-status" value="{{ $user->status ?? 'AKTIF' }}">
                    
                    <input type="hidden" name="nama_x" id="val-nama-x" value="530">
                    <input type="hidden" name="nama_y" id="val-nama-y" value="460">
                    
                    <input type="hidden" name="id_x" id="val-id-x" value="530">
                    <input type="hidden" name="id_y" id="val-id-y" value="560">

                    <input type="hidden" name="status_x" id="val-status-x" value="530">
                    <input type="hidden" name="status_y" id="val-status-y" value="660">

                    <button type="submit" class="btn btn-primary" style="padding: 15px 45px; font-size: 1.2rem; border-radius: 50px; font-weight: 800; background: linear-gradient(135deg, #22c55e, #10b981); border: none; box-shadow: 0 15px 40px rgba(34, 197, 94, 0.4); transition: all 0.3s ease;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 12px; vertical-align: middle;">
                          <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                          <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                        </svg>
                        Simpan & Unduh Kartu
                    </button>
                </form>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const canvas = document.getElementById('card-canvas');
                    const wrapper = document.getElementById('canvas-wrapper');
                    let currentScale = 0.5;

                    function resizeCanvas() {
                        const wrapperWidth = wrapper.offsetWidth;
                        currentScale = wrapperWidth / 1748;
                        canvas.style.transform = `scale(${currentScale})`;
                        wrapper.style.height = (1240 * currentScale) + 'px';
                    }

                    window.addEventListener('resize', resizeCanvas);
                    resizeCanvas();

                    let activeEl = null;
                    let initialX = 0, initialY = 0;
                    let offsetX = 0, offsetY = 0;

                    const draggables = document.querySelectorAll('.draggable-text');
                    
                    function startDrag(e, el) {
                        const tag = e.target.tagName.toLowerCase();
                        if (tag === 'span' || tag === 'select' || tag === 'option') return;
                        activeEl = el;
                        
                        initialX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                        initialY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
                        
                        offsetX = parseFloat(el.style.left) || 0;
                        offsetY = parseFloat(el.style.top) || 0;
                        
                        el.style.zIndex = '1000';
                        el.style.transform = 'scale(1.08)';
                        el.style.transition = 'none';
                    }

                    function doDrag(e) {
                        if (!activeEl) return;
                        if (e.type.includes('touch')) e.preventDefault();
                        
                        let currentX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                        let currentY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;

                        let deltaX = (currentX - initialX);
                        let deltaY = (currentY - initialY);
                        
                        // Use dynamic scale for precision
                        let newX = offsetX + (deltaX * (1/currentScale));
                        let newY = offsetY + (deltaY * (1/currentScale));
                        
                        activeEl.style.left = newX + 'px';
                        activeEl.style.top = newY + 'px';
                    }

                    function stopDrag() {
                        if (!activeEl) return;
                        
                        let newX = parseFloat(activeEl.style.left);
                        let newY = parseFloat(activeEl.style.top);
                        
                        if(activeEl.id === 'drag-nama') {
                            document.getElementById('val-nama-x').value = newX;
                            document.getElementById('val-nama-y').value = newY;
                        } else if(activeEl.id === 'drag-id') {
                            document.getElementById('val-id-x').value = newX;
                            document.getElementById('val-id-y').value = newY;
                        } else if(activeEl.id === 'drag-status') {
                            document.getElementById('val-status-x').value = newX;
                            document.getElementById('val-status-y').value = newY;
                        }

                        activeEl.style.zIndex = 'auto';
                        activeEl.style.transform = 'scale(1)';
                        activeEl.style.transition = 'transform 0.2s';
                        activeEl = null;
                    }

                    draggables.forEach(el => {
                        el.addEventListener('mousedown', (e) => startDrag(e, el));
                        el.addEventListener('touchstart', (e) => startDrag(e, el), {passive: false});
                    });

                    document.addEventListener('mousemove', doDrag);
                    document.addEventListener('touchmove', doDrag, {passive: false});

                    document.addEventListener('mouseup', stopDrag);
                    document.addEventListener('touchend', stopDrag);

                    document.getElementById('editor-form').addEventListener('submit', function(e) {
                        document.getElementById('val-nama').value = document.getElementById('input-nama').innerText.trim();
                        document.getElementById('val-status').value = document.getElementById('input-status').value;
                    });
                });
            </script>
        </div>
    </div>
@endsection
