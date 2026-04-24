@extends('layouts.admin')
@section('admin_content')
    <div class="animate-fade-in">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Pratinjau Kartu Anggota</h2>
            <a href="{{ route('admin.members') }}" class="btn btn-outline">Kembali ke Daftar</a>
        </div>
        
        <div class="card p-0 glass text-center" style="overflow: hidden;">
            <div style="background: rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1); padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h4 class="mb-0" style="color: var(--primary);">Editor Kartu Digital: {{ $user->name }}</h4>
                <p class="text-muted mt-2" style="font-size: 14px; margin: 0;">Sesuaikan posisi teks dengan menggeser (drag & drop). Klik nama untuk mengedit.</p>
            </div>
            
            <div style="padding: 2rem; background: rgba(0,0,0,0.02);">
                <div style="width: 100%; max-width: 874px; height: 620px; overflow: hidden; margin: 0 auto; border: 1px solid var(--border-color); border-radius: 16px; box-shadow: var(--shadow-lg); background: #fff; position: relative;">
                    <div id="card-canvas" style="position: absolute; left: 0; top: 0; width: 1748px; height: 1240px; background: url('{{ asset('images/template_kartu.png') }}') no-repeat; transform: scale(0.5); transform-origin: top left;">
                        
                        <div id="drag-nama" class="draggable-text" style="position: absolute; left: 530px; top: 460px; font-size: 60px; font-weight: bold; font-family: 'Inter', Arial, sans-serif; color: white; cursor: move; white-space: normal; max-width: 850px; line-height: 1.15; user-select: none; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                            NAMA : <span contenteditable="true" id="input-nama" style="outline: 2px dashed rgba(255,255,255,0.7); padding: 5px 10px; cursor: text; word-wrap: break-word; border-radius: 8px; background: rgba(0,0,0,0.1);">{{ strtoupper($user->name) }}</span>
                        </div>

                        <div id="drag-id" class="draggable-text" style="position: absolute; left: 530px; top: 560px; font-size: 60px; font-family: 'Inter', Arial, sans-serif; color: white; cursor: move; white-space: nowrap; user-select: none; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                            ID : {{ $user->member_id ?? 'JC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                        </div>

                        <div id="drag-status" class="draggable-text" style="position: absolute; left: 530px; top: 660px; font-size: 60px; font-family: 'Inter', Arial, sans-serif; color: white; cursor: move; white-space: nowrap; user-select: none; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                            STATUS : 
                            <select id="input-status" style="background: rgba(0,0,0,0.1); color: white; border: 2px dashed rgba(255,255,255,0.7); border-radius: 8px; padding: 0 10px; font-size: inherit; font-family: inherit; cursor: pointer; outline: none; font-weight: bold;">
                                <option style="color: black;" value="AKTIF" {{ ($user->status ?? 'AKTIF') === 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                                <option style="color: black;" value="TIDAK AKTIF" {{ ($user->status ?? 'AKTIF') === 'TIDAK AKTIF' ? 'selected' : '' }}>TIDAK AKTIF</option>
                            </select>
                        </div>

                        <!-- Visual QR placeholder -->
                        <div id="qr-box" style="position: absolute; left: 1114px; top: 720px; width: 345px; height: 345px; background: rgba(255,255,255,0.9); border: 4px dashed rgba(0,0,0,0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 0 20px rgba(0,0,0,0.05);">
                            <div style="text-align: center;">
                                <svg width="80" height="80" fill="none" stroke="rgba(0,0,0,0.3)" stroke-width="1.5" viewBox="0 0 24 24" style="margin-bottom: 10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-1.5m0 0H8m-2.5 0H4m8-4v1m0 0v1.5m0 0V19m0-1.5H8m4 0h4m-8-4H4m4 0v-1m0 0v-1.5m0 0V8m0 1.5H4m8-4h4m0 0v1.5m0 0V8m0-1.5h2m-6 0h-1.5M16 12h-1.5m0 0H12m4 0v1.5m0 0V16m0-1.5h2"></path></svg>
                                <div style="font-size: 30px; font-weight: bold; color: rgba(0,0,0,0.4); font-family: 'Inter', sans-serif;">QR CODE</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="padding: 1.5rem; background: var(--surface-color); border-top: 1px solid var(--border-color);">
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

                    <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 1.1rem; border-radius: 30px; box-shadow: 0 10px 20px rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                          <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                          <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                        </svg>
                        Simpan & Unduh Kartu Final
                    </button>
                </form>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const canvas = document.getElementById('card-canvas');
                    let activeEl = null;
                    let initialX = 0, initialY = 0;
                    let offsetX = 0, offsetY = 0;

                    const draggables = document.querySelectorAll('.draggable-text');
                    
                    draggables.forEach(el => {
                        el.addEventListener('mousedown', function(e) {
                            const tag = e.target.tagName.toLowerCase();
                            if (tag === 'span' || tag === 'select' || tag === 'option') return; // biarkan bisa diselect/diedit
                            activeEl = el;
                            
                            // Adjust for 0.5 scale
                            initialX = e.clientX;
                            initialY = e.clientY;
                            
                            // The current left/top styles
                            offsetX = parseFloat(el.style.left) || 0;
                            offsetY = parseFloat(el.style.top) || 0;
                            
                            el.style.border = '1px dashed #fff';
                            el.style.boxShadow = '0 0 10px rgba(0,0,0,0.5)';
                        });
                    });

                    document.addEventListener('mousemove', function(e) {
                        if (!activeEl) return;
                        
                        // mouse delta
                        let deltaX = (e.clientX - initialX);
                        let deltaY = (e.clientY - initialY);
                        
                        // div scaled by 0.5, so 1px physical move = 2px virtual move
                        let newX = offsetX + (deltaX * 2);
                        let newY = offsetY + (deltaY * 2);
                        
                        activeEl.style.left = newX + 'px';
                        activeEl.style.top = newY + 'px';
                    });

                    document.addEventListener('mouseup', function(e) {
                        if (!activeEl) return;
                        
                        // Save coordinates to hidden inputs based on element ID
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

                        activeEl.style.border = 'none';
                        activeEl.style.boxShadow = 'none';
                        activeEl = null;
                    });

                    document.getElementById('editor-form').addEventListener('submit', function(e) {
                        // Capture editable contents before submitting
                        document.getElementById('val-nama').value = document.getElementById('input-nama').innerText.trim();
                        document.getElementById('val-status').value = document.getElementById('input-status').value;
                    });
                });
            </script>
        </div>
    </div>
@endsection
