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
        display: none; 
        position: fixed; 
        top: 0; left: 0;
        width: 100%; height: 100%; 
        background: rgba(0,0,0,0.92);
        z-index: 99999; 
        flex-direction: column; 
        align-items: center;
        justify-content: center; 
        color: white; 
        text-align: center;
        padding: 2rem; 
        backdrop-filter: blur(20px);
    }

    .phone-icon {
        width: 80px; height: 140px; 
        border: 5px solid #22c55e;
        border-radius: 18px; 
        margin-bottom: 2rem; 
        position: relative;
        animation: tilt-phone 2.5s ease-in-out infinite;
        box-shadow: 0 0 40px rgba(34, 197, 94, 0.5);
    }

    .phone-icon::after {
        content: ''; 
        position: absolute; 
        bottom: 14px; 
        left: 50%;
        transform: translateX(-50%); 
        width: 18px; 
        height: 18px;
        border: 3px solid #22c55e; 
        border-radius: 50%;
    }

    @media (max-width: 991px) and (orientation: portrait) {
        #landscape-warning { display: flex; }
    }

    .editor-badge {
        display: inline-flex; 
        align-items: center; 
        gap: 8px;
        padding: 6px 18px; 
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e; 
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 30px; 
        font-size: 12px; 
        font-weight: 800;
        letter-spacing: 1px;
    }

    .pulse-animation {
        width: 8px; height: 8px; 
        background: #22c55e;
        border-radius: 50%; 
        display: inline-block;
        animation: pulse-green 2s infinite;
    }

    #canvas-wrapper {
        width: 100%; 
        max-width: 874px; 
        margin: 0 auto;
        overflow: hidden; 
        border: 14px solid #fff;
        border-radius: 32px; 
        box-shadow: 0 40px 90px rgba(0,0,0,0.25);
        background: #fff; 
        position: relative;
    }

    #card-canvas {
        position: absolute; 
        left: 0; 
        top: 0;
        width: 1748px; 
        height: 1240px;
        background: url('{{ asset('images/template_kartu.png') }}') no-repeat center;
        background-size: cover;
        transform-origin: top left;
        box-shadow: inset 0 0 80px rgba(0,0,0,0.15);
    }

    .draggable-text {
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .draggable-text:hover {
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.3);
    }

    .guide-line {
        position: absolute; 
        pointer-events: none; 
        z-index: 9999;
        transition: opacity 0.1s;
    }

    .guide-h { left: 0; width: 100%; height: 2px; background: #e11d48; }
    .guide-v { top: 0; height: 100%; width: 2px; background: #e11d48; }

    .snap-dot {
        position: absolute; 
        width: 12px; 
        height: 12px;
        background: #e11d48; 
        border-radius: 50%;
        pointer-events: none; 
        z-index: 10000; 
        opacity: 0;
        transform: translate(-50%, -50%);
        box-shadow: 0 0 12px rgba(225, 29, 72, 0.7);
    }

    .toolbar-panel {
        background: var(--surface-color);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<!-- Landscape Warning -->
<div id="landscape-warning">
    <div class="phone-icon"></div>
    <h2 style="font-weight:800; color:#22c55e;">Miringkan Layar Anda</h2>
    <p style="max-width:420px; opacity:0.85; margin-top:1rem;">
        Editor kartu digital paling optimal digunakan dalam mode <strong>landscape</strong>.
    </p>
    <button onclick="document.getElementById('landscape-warning').style.display='none'"
            style="margin-top:2rem; padding:12px 32px; border-radius:50px; border:2px solid white; background:transparent; color:white; font-weight:700; cursor:pointer;">
        Tetap Lanjutkan
    </button>
</div>

<div class="container" style="padding-top:2rem; padding-bottom:4rem;">
    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div>
            <div class="editor-badge">
                <span class="pulse-animation"></span>
                MODE EDITOR KARTU AKTIF
            </div>
            <h2 style="font-weight:800; margin:0;">Digital ID Card Designer</h2>
            <p style="color:var(--text-secondary); margin:0.25rem 0 0;">Seret teks • Edit nama • Snap otomatis ke garis tengah</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline" style="border-radius:30px; padding:10px 22px;">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div style="display:grid; grid-template-columns:1fr 260px; gap:1.5rem; align-items:start;">
        
        <!-- CANVAS -->
        <div class="glass" style="border-radius:20px; overflow:hidden; padding:1.5rem; text-align:center;">
            <div id="canvas-wrapper">
                <div id="card-canvas">
                    <!-- Guide Lines -->
                    <div class="guide-line guide-h guide-center-h" id="guide-h-center" style="top:50%; opacity:0.25;"></div>
                    <div class="guide-line guide-v guide-center-v" id="guide-v-center" style="left:50%; opacity:0.25;"></div>
                    <div class="guide-line guide-h" id="guide-h-drag" style="top:0; opacity:0;"></div>
                    <div class="guide-line guide-v" id="guide-v-drag" style="left:0; opacity:0;"></div>
                    <div class="snap-dot" id="snap-dot"></div>

                    <!-- NAMA -->
                    <div id="drag-nama" class="draggable-text" style="position:absolute; left:530px; top:460px; font-size:60px; font-weight:800; font-family:'Inter',sans-serif; color:white; cursor:move; white-space:normal; max-width:850px; line-height:1.1; user-select:none; text-shadow:3px 3px 10px rgba(0,0,0,0.6); touch-action:none;">
                        NAMA : <span contenteditable="true" id="input-nama" style="outline:2px dashed rgba(255,255,255,0.6); padding:4px 18px; cursor:text; border-radius:10px; background:rgba(0,0,0,0.25);">{{ strtoupper($user->name) }}</span>
                    </div>

                    <!-- ID -->
                    <div id="drag-id" class="draggable-text" style="position:absolute; left:530px; top:570px; font-size:52px; font-family:'Inter',sans-serif; color:white; cursor:move; white-space:nowrap; user-select:none; text-shadow:3px 3px 10px rgba(0,0,0,0.6); touch-action:none;">
                        ID : {{ $user->member_id ?? 'JC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                    </div>

                    <!-- STATUS -->
                    <div id="drag-status" class="draggable-text" style="position:absolute; left:530px; top:670px; font-size:52px; font-family:'Inter',sans-serif; color:white; cursor:move; white-space:nowrap; user-select:none; text-shadow:3px 3px 10px rgba(0,0,0,0.6); touch-action:none;">
                        STATUS :
                        <select id="input-status" style="background:rgba(0,0,0,0.25); color:white; border:2px dashed rgba(255,255,255,0.6); border-radius:10px; padding:2px 18px; font-size:inherit; font-weight:700; cursor:pointer;">
                            <option style="color:black;" value="AKTIF" {{ ($user->status ?? 'AKTIF') === 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                            <option style="color:black;" value="TIDAK AKTIF" {{ ($user->status ?? 'AKTIF') === 'TIDAK AKTIF' ? 'selected' : '' }}>TIDAK AKTIF</option>
                        </select>
                    </div>

                    <!-- QR BOX -->
                    <div id="qr-box" style="position:absolute; left:1114px; top:720px; width:345px; height:345px; background:rgba(255,255,255,0.95); border:5px dashed rgba(0,0,0,0.15); border-radius:24px; display:flex; align-items:center; justify-content:center; box-shadow:inset 0 0 50px rgba(0,0,0,0.08);">
                        <div style="text-align:center;">
                            <div style="font-size:42px; font-weight:900; color:rgba(0,0,0,0.25); letter-spacing:4px;">LIVE QR</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div>
            <!-- Panduan -->
            <div class="toolbar-panel">
                <h5 style="font-size:0.75rem; font-weight:800; color:var(--text-secondary); letter-spacing:1px;">📌 CARA PENGGUNAAN</h5>
                <ul style="font-size:0.82rem; color:var(--text-secondary); padding-left:1.1rem; line-height:1.7; margin:0;">
                    <li><strong>Seret</strong> area teks untuk memindahkan</li>
                    <li><strong>Klik nama</strong> untuk mengedit teks</li>
                    <li>Garis merah = panduan snap otomatis</li>
                    <li>Klik <strong>Reset</strong> untuk kembalikan posisi</li>
                </ul>
            </div>

            <!-- Koordinat Manual -->
            <div class="toolbar-panel">
                <h5 style="font-size:0.75rem; font-weight:800; color:var(--text-secondary); letter-spacing:1px;">🎯 KOORDINAT MANUAL</h5>
                
                <div style="display:grid; gap:0.85rem; font-size:0.8rem;">
                    <!-- Nama -->
                    <div>
                        <label style="font-weight:700; color:var(--text-secondary);">NAMA (X / Y)</label>
                        <div style="display:flex; gap:6px; margin-top:4px;">
                            <input type="number" id="ui-nama-x" value="530" style="flex:1; padding:7px 10px; border-radius:8px; border:1px solid var(--border-color); background:var(--surface-color); color:var(--text-primary);">
                            <input type="number" id="ui-nama-y" value="460" style="flex:1; padding:7px 10px; border-radius:8px; border:1px solid var(--border-color); background:var(--surface-color); color:var(--text-primary);">
                        </div>
                    </div>
                    
                    <!-- ID -->
                    <div>
                        <label style="font-weight:700; color:var(--text-secondary);">ID (X / Y)</label>
                        <div style="display:flex; gap:6px; margin-top:4px;">
                            <input type="number" id="ui-id-x" value="530" style="flex:1; padding:7px 10px; border-radius:8px; border:1px solid var(--border-color); background:var(--surface-color); color:var(--text-primary);">
                            <input type="number" id="ui-id-y" value="570" style="flex:1; padding:7px 10px; border-radius:8px; border:1px solid var(--border-color); background:var(--surface-color); color:var(--text-primary);">
                        </div>
                    </div>

                    <button onclick="applyManualPositions()" 
                            style="padding:9px; border-radius:10px; border:none; background:#22c55e; color:white; font-weight:700; cursor:pointer; font-size:0.85rem;">
                        Terapkan Posisi Manual
                    </button>
                </div>
            </div>

            <!-- Form Submit -->
            <form id="editor-form" action="{{ route('member.card.download') }}" method="POST">
                @csrf
                <input type="hidden" name="nama_text" id="val-nama">
                <input type="hidden" name="status_text" id="val-status">
                <input type="hidden" name="nama_x" id="val-nama-x" value="530">
                <input type="hidden" name="nama_y" id="val-nama-y" value="460">
                <input type="hidden" name="id_x" id="val-id-x" value="530">
                <input type="hidden" name="id_y" id="val-id-y" value="570">
                <input type="hidden" name="status_x" id="val-status-x" value="530">
                <input type="hidden" name="status_y" id="val-status-y" value="670">

                <button type="submit" id="submit-btn"
                        style="width:100%; padding:14px; font-size:1rem; border-radius:16px; font-weight:800; background:linear-gradient(135deg, #22c55e, #10b981); border:none; color:white; box-shadow:0 10px 30px rgba(34,197,94,0.4);">
                    <span id="btn-text">💾 Simpan & Unduh Kartu</span>
                </button>

                <button type="button" onclick="resetAllPositions()" 
                        style="width:100%; margin-top:10px; padding:10px; border-radius:12px; background:transparent; border:2px solid var(--border-color); color:var(--text-secondary); font-weight:600;">
                    Reset Semua Posisi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('card-canvas');
    const wrapper = document.getElementById('canvas-wrapper');
    
    const CANVAS_W = 1748;
    const CANVAS_H = 1240;
    const SNAP_THRESHOLD = 35;

    let currentScale = 0.5;
    let activeEl = null;
    let initialX = 0, initialY = 0;
    let offsetX = 0, offsetY = 0;
    let hasChanges = false;

    // Elements
    const guideHCenter = document.getElementById('guide-h-center');
    const guideVCenter = document.getElementById('guide-v-center');
    const guideHDrag   = document.getElementById('guide-h-drag');
    const guideVDrag   = document.getElementById('guide-v-drag');
    const snapDot      = document.getElementById('snap-dot');

    const snapPointsX = [0, CANVAS_W / 2, CANVAS_W];
    const snapPointsY = [0, CANVAS_H / 2, CANVAS_H];

    // Resize Canvas
    function resizeCanvas() {
        const wrapperWidth = wrapper.offsetWidth;
        currentScale = wrapperWidth / CANVAS_W;
        canvas.style.transform = `scale(${currentScale})`;
        wrapper.style.height = (CANVAS_H * currentScale) + 'px';
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    // Snap Logic
    function checkSnap(x, y) {
        let snappedX = x, snappedY = y;
        let snapped = false;

        // Vertical snap
        for (let sx of snapPointsX) {
            if (Math.abs(x - sx) < SNAP_THRESHOLD) {
                snappedX = sx;
                guideVDrag.style.left = sx + 'px';
                guideVDrag.style.opacity = '1';
                if (sx === CANVAS_W / 2) guideVCenter.style.opacity = '0.6';
                snapped = true;
                break;
            }
        }
        if (!snapped) {
            guideVDrag.style.opacity = '0';
            guideVCenter.style.opacity = '0.25';
        }

        // Horizontal snap
        snapped = false;
        for (let sy of snapPointsY) {
            if (Math.abs(y - sy) < SNAP_THRESHOLD) {
                snappedY = sy;
                guideHDrag.style.top = sy + 'px';
                guideHDrag.style.opacity = '1';
                if (sy === CANVAS_H / 2) guideHCenter.style.opacity = '0.6';
                snapped = true;
                break;
            }
        }
        if (!snapped) {
            guideHDrag.style.opacity = '0';
            guideHCenter.style.opacity = '0.25';
        }

        if (Math.abs(snappedX - x) < SNAP_THRESHOLD && Math.abs(snappedY - y) < SNAP_THRESHOLD) {
            snapDot.style.left = snappedX + 'px';
            snapDot.style.top = snappedY + 'px';
            snapDot.style.opacity = '1';
        } else {
            snapDot.style.opacity = '0';
        }

        return { x: snappedX, y: snappedY };
    }

    function hideGuides() {
        [guideHCenter, guideVCenter, guideHDrag, guideVDrag, snapDot].forEach(el => {
            el.style.opacity = (el.id.includes('center')) ? '0.25' : '0';
        });
    }

    // Drag Functions
    function startDrag(e, el) {
        const tag = e.target.tagName.toLowerCase();
        // Jangan drag jika klik pada span contenteditable atau select
        if (tag === 'span' || tag === 'select' || tag === 'option') {
            return;
        }

        activeEl = el;
        initialX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
        initialY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
        offsetX = parseFloat(el.style.left) || 0;
        offsetY = parseFloat(el.style.top) || 0;

        el.style.zIndex = '1000';
        el.style.transform = 'scale(1.03)';
        el.style.transition = 'none';

        guideHCenter.style.opacity = '0.5';
        guideVCenter.style.opacity = '0.5';
    }

    function doDrag(e) {
        if (!activeEl) return;
        if (e.type.includes('touch')) e.preventDefault();

        const cX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
        const cY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;

        let rawX = offsetX + ((cX - initialX) / currentScale);
        let rawY = offsetY + ((cY - initialY) / currentScale);

        const snapped = checkSnap(rawX, rawY);
        activeEl.style.left = snapped.x + 'px';
        activeEl.style.top = snapped.y + 'px';

        updateToolbarInputs(activeEl.id, snapped.x, snapped.y);
        hasChanges = true;
    }

    function stopDrag() {
        if (!activeEl) return;

        const newX = parseFloat(activeEl.style.left);
        const newY = parseFloat(activeEl.style.top);

        syncHiddenInputs(activeEl.id, newX, newY);
        activeEl.style.zIndex = 'auto';
        activeEl.style.transform = 'scale(1)';
        activeEl.style.transition = 'transform 0.2s ease';

        activeEl = null;
        setTimeout(hideGuides, 350);
    }

    // Event Listeners
    const draggables = document.querySelectorAll('.draggable-text');
    draggables.forEach(el => {
        el.addEventListener('mousedown', (e) => startDrag(e, el));
        el.addEventListener('touchstart', (e) => startDrag(e, el), { passive: false });
    });

    document.addEventListener('mousemove', doDrag);
    document.addEventListener('touchmove', doDrag, { passive: false });
    document.addEventListener('mouseup', stopDrag);
    document.addEventListener('touchend', stopDrag);

    // Sync functions
    function syncHiddenInputs(id, x, y) {
        const map = {
            'drag-nama':   { x: 'val-nama-x',   y: 'val-nama-y' },
            'drag-id':     { x: 'val-id-x',     y: 'val-id-y' },
            'drag-status': { x: 'val-status-x', y: 'val-status-y' }
        };
        if (map[id]) {
            document.getElementById(map[id].x).value = Math.round(x);
            document.getElementById(map[id].y).value = Math.round(y);
        }
    }

    function updateToolbarInputs(id, x, y) {
        const map = {
            'drag-nama':   { x: 'ui-nama-x',   y: 'ui-nama-y' },
            'drag-id':     { x: 'ui-id-x',     y: 'ui-id-y' },
            'drag-status': { x: 'ui-status-x', y: 'ui-status-y' }
        };
        if (map[id]) {
            document.getElementById(map[id].x).value = Math.round(x);
            document.getElementById(map[id].y).value = Math.round(y);
        }
    }

    window.applyManualPositions = function () {
        const map = [
            { el: 'drag-nama',   xi: 'ui-nama-x',   yi: 'ui-nama-y' },
            { el: 'drag-id',     xi: 'ui-id-x',     yi: 'ui-id-y' },
            { el: 'drag-status', xi: 'ui-status-x', yi: 'ui-status-y' }
        ];
        map.forEach(m => {
            const x = parseFloat(document.getElementById(m.xi).value) || 0;
            const y = parseFloat(document.getElementById(m.yi).value) || 0;
            document.getElementById(m.el).style.left = x + 'px';
            document.getElementById(m.el).style.top = y + 'px';
            syncHiddenInputs(m.el, x, y);
        });
        hasChanges = true;
    };

    window.resetAllPositions = function () {
        if (!confirm('Reset semua posisi teks ke default?')) return;

        document.getElementById('drag-nama').style.left = '530px';
        document.getElementById('drag-nama').style.top = '460px';
        document.getElementById('drag-id').style.left = '530px';
        document.getElementById('drag-id').style.top = '570px';
        document.getElementById('drag-status').style.left = '530px';
        document.getElementById('drag-status').style.top = '670px';

        // Reset hidden inputs
        document.getElementById('val-nama-x').value = 530;
        document.getElementById('val-nama-y').value = 460;
        document.getElementById('val-id-x').value = 530;
        document.getElementById('val-id-y').value = 570;
        document.getElementById('val-status-x').value = 530;
        document.getElementById('val-status-y').value = 670;

        // Reset UI inputs
        document.getElementById('ui-nama-x').value = 530;
        document.getElementById('ui-nama-y').value = 460;
        document.getElementById('ui-id-x').value = 530;
        document.getElementById('ui-id-y').value = 570;
        document.getElementById('ui-status-x').value = 530;
        document.getElementById('ui-status-y').value = 670;

        hasChanges = false;
    };

    // Form Submit
    const form = document.getElementById('editor-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');

    form.addEventListener('submit', function () {
        document.getElementById('val-nama').value = document.getElementById('input-nama').innerText.trim();
        document.getElementById('val-status').value = document.getElementById('input-status').value;

        submitBtn.disabled = true;
        btnText.innerHTML = '⏳ Memproses...';
    });

    // Warn before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function (e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
@endpush