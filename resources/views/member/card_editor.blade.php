@extends('layouts.app')

@section('title', 'Editor Kartu Digital - Jostru')

@push('styles')
<style>
    body { padding-bottom: 0 !important; }

    @keyframes tilt-phone {
        0% { transform: rotate(0deg); }
        50% { transform: rotate(-90deg); }
        100% { transform: rotate(0deg); }
    }

    #landscape-warning {
        display: none; position: fixed; top: 0; left: 0;
        width: 100%; height: 100%; background: rgba(0,0,0,0.95);
        z-index: 9999; flex-direction: column; align-items: center;
        justify-content: center; color: white; text-align: center;
        padding: 2rem; backdrop-filter: blur(15px);
    }
    .phone-icon {
        width: 80px; height: 140px; border: 4px solid var(--primary);
        border-radius: 12px; margin-bottom: 2rem; position: relative;
        animation: tilt-phone 3s ease-in-out infinite;
        box-shadow: 0 0 30px rgba(16,185,129,0.4);
    }
    .phone-icon::after {
        content: ''; position: absolute; bottom: 10px; left: 50%;
        transform: translateX(-50%); width: 15px; height: 15px;
        border: 2px solid var(--primary); border-radius: 50%;
    }
    @media (max-width: 991px) and (orientation: portrait) {
        #landscape-warning { display: flex; }
    }

    .editor-badge {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 16px; background: rgba(16,185,129,0.1);
        color: var(--primary); border: 1px solid rgba(16,185,129,0.25);
        border-radius: 20px; font-size: 12px; font-weight: 800;
        margin-bottom: 15px; letter-spacing: 1px;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16,185,129,0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16,185,129,0); }
    }
    .pulse-animation {
        width: 8px; height: 8px; background: var(--primary);
        border-radius: 50%; display: inline-block;
        animation: pulse-green 2s infinite;
    }

    #canvas-wrapper {
        width: 100%; max-width: 874px; margin: 0 auto;
        overflow: hidden; border: 12px solid #fff;
        border-radius: 28px; box-shadow: 0 40px 80px rgba(0,0,0,0.2);
        background: #fff; position: relative;
    }
    #card-canvas {
        position: absolute; left: 0; top: 0;
        width: 1748px; height: 1240px;
        background: url('{{ asset('images/template_kartu.png') }}') no-repeat;
        background-size: cover;
        transform-origin: top left;
    }

    /* Snapping Guide Lines (Canva-style) */
    .guide-line {
        position: absolute; pointer-events: none; z-index: 9999;
        transition: opacity 0.1s;
    }
    .guide-h { left: 0; width: 100%; height: 2px; background: #e11d48; opacity: 0; }
    .guide-v { top: 0; height: 100%; width: 2px; background: #e11d48; opacity: 0; }
    .guide-center-h { top: 50%; }
    .guide-center-v { left: 50%; }

    /* Snap indicator dot */
    .snap-dot {
        position: absolute; width: 10px; height: 10px;
        background: #e11d48; border-radius: 50%;
        pointer-events: none; z-index: 10000; opacity: 0;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 8px rgba(225,29,72,0.6);
    }

    .toolbar-panel {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .toolbar-panel h5 {
        font-size: 0.8rem; font-weight: 800; letter-spacing: 0.08em;
        color: var(--text-secondary); text-transform: uppercase; margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div id="landscape-warning">
    <div class="phone-icon"></div>
    <h2 style="font-weight:800;color:var(--primary);">Miringkan Layar Anda</h2>
    <p style="opacity:0.8;max-width:400px;margin-top:1rem;">Halaman editor optimal dalam posisi landscape.</p>
    <button onclick="document.getElementById('landscape-warning').style.display='none'"
        style="margin-top:1.5rem;padding:10px 25px;border-radius:30px;border:2px solid white;background:transparent;color:white;cursor:pointer;font-weight:700;">
        Tetap Lanjutkan
    </button>
</div>

<div class="container" style="padding-top:2rem;padding-bottom:4rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <div class="editor-badge">
                <span class="pulse-animation"></span>
                MODE EDITOR KARTU AKTIF
            </div>
            <h2 style="font-weight:800;margin:0;">Digital ID Card Designer</h2>
            <p style="color:var(--text-secondary);margin:0.25rem 0 0;">Seret & atur teks. Garis merah = panduan perataan otomatis ✨</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="border-radius:30px;padding:10px 20px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:8px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 240px;gap:1.5rem;align-items:start;">
        {{-- CANVAS AREA --}}
        <div class="glass" style="border-radius:var(--radius-lg);overflow:hidden;padding:1.5rem;text-align:center;">
            <div id="canvas-wrapper">
                <div id="card-canvas">
                    {{-- Guide Lines --}}
                    <div class="guide-line guide-h guide-center-h" id="guide-h-center"></div>
                    <div class="guide-line guide-v guide-center-v" id="guide-v-center"></div>
                    <div class="guide-line guide-h" id="guide-h-drag" style="top:0;"></div>
                    <div class="guide-line guide-v" id="guide-v-drag" style="left:0;"></div>
                    <div class="snap-dot" id="snap-dot"></div>

                    <div id="drag-nama" class="draggable-text" style="position:absolute;left:530px;top:460px;font-size:60px;font-weight:bold;font-family:'Inter',Arial,sans-serif;color:white;cursor:move;white-space:normal;max-width:850px;line-height:1.15;user-select:none;text-shadow:3px 3px 8px rgba(0,0,0,0.5);touch-action:none;">
                        NAMA : <span contenteditable="true" id="input-nama" style="outline:2px dashed rgba(255,255,255,0.7);padding:5px 20px;cursor:text;word-wrap:break-word;border-radius:12px;background:rgba(0,0,0,0.2);">{{ strtoupper($user->name) }}</span>
                    </div>

                    <div id="drag-id" class="draggable-text" style="position:absolute;left:530px;top:560px;font-size:60px;font-family:'Inter',Arial,sans-serif;color:white;cursor:move;white-space:nowrap;user-select:none;text-shadow:3px 3px 8px rgba(0,0,0,0.5);touch-action:none;">
                        ID : {{ $user->member_id ?? 'JC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                    </div>

                    <div id="drag-status" class="draggable-text" style="position:absolute;left:530px;top:660px;font-size:60px;font-family:'Inter',Arial,sans-serif;color:white;cursor:move;white-space:nowrap;user-select:none;text-shadow:3px 3px 8px rgba(0,0,0,0.5);touch-action:none;">
                        STATUS :
                        <select id="input-status" style="background:rgba(0,0,0,0.2);color:white;border:2px dashed rgba(255,255,255,0.7);border-radius:12px;padding:0 20px;font-size:inherit;font-family:inherit;cursor:pointer;outline:none;font-weight:bold;">
                            <option style="color:black;" value="AKTIF" {{ ($user->status ?? 'AKTIF') === 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                            <option style="color:black;" value="TIDAK AKTIF" {{ ($user->status ?? 'AKTIF') === 'TIDAK AKTIF' ? 'selected' : '' }}>TIDAK AKTIF</option>
                        </select>
                    </div>

                    <div id="qr-box" style="position:absolute;left:1114px;top:720px;width:345px;height:345px;background:rgba(255,255,255,0.98);border:4px dashed rgba(0,0,0,0.1);border-radius:24px;display:flex;align-items:center;justify-content:center;box-shadow:inset 0 0 40px rgba(0,0,0,0.05);">
                        <div style="text-align:center;">
                            <svg width="100" height="100" fill="none" stroke="rgba(0,0,0,0.2)" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-1.5m0 0H8m-2.5 0H4m8-4v1m0 0v1.5m0 0V19m0-1.5H8m4 0h4m-8-4H4m4 0v-1m0 0v-1.5m0 0V8m0 1.5H4m8-4h4m0 0v1.5m0 0V8m0-1.5h2m-6 0h-1.5M16 12h-1.5m0 0H12m4 0v1.5m0 0V16m0-1.5h2"></path></svg>
                            <div style="font-size:38px;font-weight:900;color:rgba(0,0,0,0.3);font-family:'Inter',sans-serif;letter-spacing:3px;">LIVE QR</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOOLBAR PANEL --}}
        <div>
            <div class="toolbar-panel">
                <h5>📌 Panduan Penggunaan</h5>
                <ul style="font-size:0.82rem;color:var(--text-secondary);padding-left:1.2rem;line-height:1.8;">
                    <li><strong>Seret</strong> teks untuk memindahkan</li>
                    <li><strong>Klik teks nama</strong> untuk mengedit</li>
                    <li><span style="color:#e11d48;font-weight:700;">Garis merah</span> = panduan perataan</li>
                    <li>Garis <strong>tengah (V/H)</strong> = sumbu kartu</li>
                    <li>Klik <strong>Unduh</strong> setelah selesai</li>
                </ul>
            </div>

            <div class="toolbar-panel">
                <h5>🎯 Posisi Teks (px)</h5>
                <div style="display:grid;gap:0.75rem;">
                    <div>
                        <label style="font-size:0.75rem;font-weight:700;color:var(--text-secondary);">NAMA — X / Y</label>
                        <div style="display:flex;gap:6px;margin-top:4px;">
                            <input type="number" id="ui-nama-x" placeholder="X" value="530" style="width:50%;padding:6px 10px;border-radius:8px;border:1px solid var(--border-color);background:var(--surface-color);color:var(--text-primary);font-size:0.8rem;">
                            <input type="number" id="ui-nama-y" placeholder="Y" value="460" style="width:50%;padding:6px 10px;border-radius:8px;border:1px solid var(--border-color);background:var(--surface-color);color:var(--text-primary);font-size:0.8rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:700;color:var(--text-secondary);">ID — X / Y</label>
                        <div style="display:flex;gap:6px;margin-top:4px;">
                            <input type="number" id="ui-id-x" placeholder="X" value="530" style="width:50%;padding:6px 10px;border-radius:8px;border:1px solid var(--border-color);background:var(--surface-color);color:var(--text-primary);font-size:0.8rem;">
                            <input type="number" id="ui-id-y" placeholder="Y" value="560" style="width:50%;padding:6px 10px;border-radius:8px;border:1px solid var(--border-color);background:var(--surface-color);color:var(--text-primary);font-size:0.8rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;font-weight:700;color:var(--text-secondary);">STATUS — X / Y</label>
                        <div style="display:flex;gap:6px;margin-top:4px;">
                            <input type="number" id="ui-status-x" placeholder="X" value="530" style="width:50%;padding:6px 10px;border-radius:8px;border:1px solid var(--border-color);background:var(--surface-color);color:var(--text-primary);font-size:0.8rem;">
                            <input type="number" id="ui-status-y" placeholder="Y" value="660" style="width:50%;padding:6px 10px;border-radius:8px;border:1px solid var(--border-color);background:var(--surface-color);color:var(--text-primary);font-size:0.8rem;">
                        </div>
                    </div>
                    <button onclick="applyManualPositions()" style="padding:8px;border-radius:10px;border:none;background:var(--primary);color:white;font-weight:700;cursor:pointer;font-size:0.85rem;">
                        ✅ Terapkan Posisi
                    </button>
                </div>
            </div>

            <form id="editor-form" action="{{ route('member.card.download') }}" method="POST">
                @csrf
                <input type="hidden" name="nama_text" id="val-nama" value="">
                <input type="hidden" name="status_text" id="val-status" value="{{ $user->status ?? 'AKTIF' }}">
                <input type="hidden" name="nama_x" id="val-nama-x" value="530">
                <input type="hidden" name="nama_y" id="val-nama-y" value="460">
                <input type="hidden" name="id_x" id="val-id-x" value="530">
                <input type="hidden" name="id_y" id="val-id-y" value="560">
                <input type="hidden" name="status_x" id="val-status-x" value="530">
                <input type="hidden" name="status_y" id="val-status-y" value="660">

                <button type="submit" class="btn btn-primary" style="width:100%;padding:14px;font-size:1rem;border-radius:16px;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--secondary));border:none;box-shadow:0 10px 30px rgba(16,185,129,0.35);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-right:8px;vertical-align:middle;"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>
                    Simpan & Unduh Kartu
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('card-canvas');
    const wrapper = document.getElementById('canvas-wrapper');
    let currentScale = 0.5;
    const CANVAS_W = 1748, CANVAS_H = 1240;
    const SNAP_THRESHOLD = 30; // px pada koordinat canvas asli

    function resizeCanvas() {
        const wrapperWidth = wrapper.offsetWidth;
        currentScale = wrapperWidth / CANVAS_W;
        canvas.style.transform = `scale(${currentScale})`;
        wrapper.style.height = (CANVAS_H * currentScale) + 'px';
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    // Guide elements
    const guideHCenter = document.getElementById('guide-h-center');
    const guideVCenter = document.getElementById('guide-v-center');
    const guideHDrag   = document.getElementById('guide-h-drag');
    const guideVDrag   = document.getElementById('guide-v-drag');
    const snapDot      = document.getElementById('snap-dot');

    // Snap points: center-H, center-V, top, left edges
    const snapPointsX = [0, CANVAS_W / 2, CANVAS_W];
    const snapPointsY = [0, CANVAS_H / 2, CANVAS_H];

    function showGuide(el, value) { el.style.opacity = '1'; }
    function hideGuides() {
        [guideHCenter, guideVCenter, guideHDrag, guideVDrag, snapDot]
            .forEach(el => el.style.opacity = '0');
    }

    function checkSnap(x, y) {
        let snappedX = x, snappedY = y, snapX = false, snapY = false;

        for (let sx of snapPointsX) {
            if (Math.abs(x - sx) < SNAP_THRESHOLD) {
                snappedX = sx; snapX = true;
                guideVDrag.style.left = sx + 'px';
                showGuide(guideVDrag);
                if (sx === CANVAS_W / 2) showGuide(guideVCenter);
                break;
            }
        }
        if (!snapX) guideVDrag.style.opacity = '0';

        for (let sy of snapPointsY) {
            if (Math.abs(y - sy) < SNAP_THRESHOLD) {
                snappedY = sy; snapY = true;
                guideHDrag.style.top = sy + 'px';
                showGuide(guideHDrag);
                if (sy === CANVAS_H / 2) showGuide(guideHCenter);
                break;
            }
        }
        if (!snapY) guideHDrag.style.opacity = '0';

        if (snapX && snapY) {
            snapDot.style.left = snappedX + 'px';
            snapDot.style.top  = snappedY + 'px';
            snapDot.style.opacity = '1';
        } else {
            snapDot.style.opacity = '0';
        }

        return { x: snappedX, y: snappedY };
    }

    // Drag logic
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
        el.style.transform = 'scale(1.05)';
        el.style.transition = 'none';
        // Show center guides while dragging
        guideHCenter.style.opacity = '0.4';
        guideVCenter.style.opacity = '0.4';
    }

    function doDrag(e) {
        if (!activeEl) return;
        if (e.type.includes('touch')) e.preventDefault();
        let cX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
        let cY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
        let rawX = offsetX + ((cX - initialX) / currentScale);
        let rawY = offsetY + ((cY - initialY) / currentScale);

        const snapped = checkSnap(rawX, rawY);
        activeEl.style.left = snapped.x + 'px';
        activeEl.style.top  = snapped.y + 'px';

        // Update toolbar inputs
        updateToolbarInputs(activeEl.id, snapped.x, snapped.y);
    }

    function stopDrag() {
        if (!activeEl) return;
        let newX = parseFloat(activeEl.style.left);
        let newY = parseFloat(activeEl.style.top);
        syncHiddenInputs(activeEl.id, newX, newY);
        activeEl.style.zIndex = 'auto';
        activeEl.style.transform = 'scale(1)';
        activeEl.style.transition = 'transform 0.2s';
        activeEl = null;
        setTimeout(hideGuides, 400);
    }

    draggables.forEach(el => {
        el.addEventListener('mousedown', (e) => startDrag(e, el));
        el.addEventListener('touchstart', (e) => startDrag(e, el), {passive: false});
    });
    document.addEventListener('mousemove', doDrag);
    document.addEventListener('touchmove', doDrag, {passive: false});
    document.addEventListener('mouseup', stopDrag);
    document.addEventListener('touchend', stopDrag);

    function syncHiddenInputs(id, x, y) {
        if (id === 'drag-nama') {
            document.getElementById('val-nama-x').value = x;
            document.getElementById('val-nama-y').value = y;
            document.getElementById('ui-nama-x').value = Math.round(x);
            document.getElementById('ui-nama-y').value = Math.round(y);
        } else if (id === 'drag-id') {
            document.getElementById('val-id-x').value = x;
            document.getElementById('val-id-y').value = y;
            document.getElementById('ui-id-x').value = Math.round(x);
            document.getElementById('ui-id-y').value = Math.round(y);
        } else if (id === 'drag-status') {
            document.getElementById('val-status-x').value = x;
            document.getElementById('val-status-y').value = y;
            document.getElementById('ui-status-x').value = Math.round(x);
            document.getElementById('ui-status-y').value = Math.round(y);
        }
    }

    function updateToolbarInputs(id, x, y) {
        if (id === 'drag-nama') {
            document.getElementById('ui-nama-x').value = Math.round(x);
            document.getElementById('ui-nama-y').value = Math.round(y);
        } else if (id === 'drag-id') {
            document.getElementById('ui-id-x').value = Math.round(x);
            document.getElementById('ui-id-y').value = Math.round(y);
        } else if (id === 'drag-status') {
            document.getElementById('ui-status-x').value = Math.round(x);
            document.getElementById('ui-status-y').value = Math.round(y);
        }
    }

    // Apply manual position from toolbar
    window.applyManualPositions = function() {
        const map = [
            { el: 'drag-nama',   xi: 'ui-nama-x',   yi: 'ui-nama-y' },
            { el: 'drag-id',     xi: 'ui-id-x',     yi: 'ui-id-y'   },
            { el: 'drag-status', xi: 'ui-status-x', yi: 'ui-status-y'},
        ];
        map.forEach(m => {
            const x = parseFloat(document.getElementById(m.xi).value) || 0;
            const y = parseFloat(document.getElementById(m.yi).value) || 0;
            document.getElementById(m.el).style.left = x + 'px';
            document.getElementById(m.el).style.top  = y + 'px';
            syncHiddenInputs(m.el, x, y);
        });
    };

    // Form submit
    document.getElementById('editor-form').addEventListener('submit', function() {
        document.getElementById('val-nama').value = document.getElementById('input-nama').innerText.trim();
        document.getElementById('val-status').value = document.getElementById('input-status').value;
    });
});
</script>
@endpush
