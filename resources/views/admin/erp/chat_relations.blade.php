@extends('layouts.admin')

@section('title', 'Manajemen Relasi Chat')

@section('admin_content')
<style>
    /* Premium Animations & Base */
    @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    @keyframes pulse-border { 0% { border-color: rgba(59, 130, 246, 0.4); } 50% { border-color: rgba(59, 130, 246, 0.8); box-shadow: 0 0 15px rgba(59,130,246,0.3); } 100% { border-color: rgba(59, 130, 246, 0.4); } }
    @keyframes slideInUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

    .glass-header { 
        background: linear-gradient(-45deg, #0f172a, #1e293b, #334155); background-size: 400% 400%; animation: gradientBG 15s ease infinite;
        color: white; border-radius: 28px; padding: 3rem 2.5rem; position: relative; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); margin-bottom: 2.5rem;
    }
    .glass-header::after { content: ''; position: absolute; top: -50%; right: -20%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(139,92,246,0.3) 0%, transparent 70%); border-radius: 50%; filter: blur(40px); }
    
    .workspace-wrapper { display: grid; grid-template-columns: 320px 1fr; gap: 2rem; align-items: start; }
    @media (max-width: 992px) { .workspace-wrapper { grid-template-columns: 1fr; } }
    
    /* Sleek Sidebar Palette */
    .palette-sidebar {
        background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.4);
        border-radius: 24px; padding: 1.5rem; position: sticky; top: 2rem; max-height: calc(100vh - 4rem); overflow-y: auto;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
    }
    .palette-sidebar::-webkit-scrollbar { width: 6px; }
    .palette-sidebar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

    .palette-group-title { font-weight: 800; color: #475569; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 1rem; border-bottom: 2px solid rgba(0,0,0,0.05); padding-bottom: 0.5rem; }

    /* Modern Draggable Pills */
    .node-pill {
        background: white; border: 1px solid rgba(0,0,0,0.06); border-radius: 14px;
        padding: 10px 16px; font-weight: 600; font-size: 0.9rem; cursor: grab;
        display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); user-select: none; margin: 0 8px 10px 0;
        position: relative; overflow: hidden;
    }
    .node-pill::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; }
    .node-pill:hover { transform: translateY(-2px); box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
    .node-pill:active { cursor: grabbing; transform: scale(0.97); }

    .node-all::before { background: #10b981; } .node-all { color: #059669; }
    .node-div::before { background: #8b5cf6; } .node-div { color: #7c3aed; }
    .node-user::before { background: #3b82f6; } .node-user { color: #2563eb; }

    /* Drop Zone / User Card */
    .user-card {
        background: white; border-radius: 24px; padding: 1.5rem; margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.03);
        animation: slideInUp 0.5s ease forwards; opacity: 0;
    }
    .user-card:nth-child(1) { animation-delay: 0.1s; } .user-card:nth-child(2) { animation-delay: 0.2s; } .user-card:nth-child(3) { animation-delay: 0.3s; }
    
    .user-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
    .user-info { display: flex; align-items: center; gap: 1rem; }
    .user-avatar { width: 48px; height: 48px; border-radius: 16px; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; color: #475569; box-shadow: inset 0 2px 4px rgba(255,255,255,0.8); }

    .drop-zone {
        background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; min-height: 100px;
        padding: 1.25rem; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex; flex-wrap: wrap; align-items: flex-start; align-content: flex-start; position: relative;
    }
    .drop-zone.drag-over { background: rgba(59, 130, 246, 0.05); border-style: solid; animation: pulse-border 1.5s infinite; }
    
    .drop-zone-placeholder { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #94a3b8; font-weight: 500; font-size: 0.9rem; pointer-events: none; transition: opacity 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px; }
    .drop-zone-placeholder i { font-size: 1.5rem; opacity: 0.5; }

    /* Pill within drop zone */
    .drop-zone .node-pill { margin: 4px; box-shadow: none; border-color: transparent; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .drop-zone .node-pill:hover { transform: none; }
    .drop-zone .node-pill button {
        background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; width: 24px; height: 24px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center; font-size: 14px; cursor: pointer; transition: all 0.2s; margin-left: 5px;
    }
    .drop-zone .node-pill button:hover { background: #ef4444; color: white; }

    /* Search Bar */
    .search-wrapper { position: relative; }
    .search-wrapper input { padding-left: 45px; border-radius: 100px; border: 2px solid #e2e8f0; font-weight: 500; height: 50px; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); transition: all 0.3s; }
    .search-wrapper input:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,0.1); background: white; }
    .search-wrapper i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; }
</style>

<div class="animate-fade-in">
    <!-- Premium Header -->
    <div class="glass-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
        <div style="position:relative; z-index:2;">
            <a href="{{ route('admin.erp.index') }}" class="text-decoration-none text-muted mb-3 d-inline-block" style="color:rgba(255,255,255,0.7) !important; font-weight:700; font-size:0.8rem; letter-spacing:1px;"><i class="fas fa-arrow-left me-2"></i> KEMBALI KE ERP</a>
            <h1 style="font-weight:900; font-size: clamp(2rem, 4vw, 2.8rem); margin-bottom:12px; letter-spacing:-1px; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">Peta Relasi Komunikasi</h1>
            <p style="color:rgba(255,255,255,0.85); font-size:1.1rem; max-width:650px; line-height: 1.6; margin:0;">Visualisasi dan kontrol jalur komunikasi antar anggota. Tarik opsi akses dari palet ke profil anggota untuk memberikan izin chat.</p>
        </div>
        <div style="position:relative; z-index:2;">
            <button class="btn d-flex align-items-center gap-2" onclick="saveConnections()" style="background: white; color: #0f172a; border-radius: 100px; font-weight: 800; padding: 16px 32px; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); transition: transform 0.2s, box-shadow 0.2s;">
                <i class="fas fa-save text-success"></i> Terapkan Relasi Baru
            </button>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="workspace-wrapper">
        <!-- Floating Palette (Sidebar) -->
        <div class="palette-sidebar">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div style="width:40px; height:40px; border-radius:12px; background:linear-gradient(135deg, #3b82f6, #2563eb); color:white; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                    <i class="fas fa-shapes"></i>
                </div>
                <h4 style="margin:0; font-weight:800; color:#1e293b;">Palet Akses</h4>
            </div>
            
            <div class="mb-4">
                <div class="palette-group-title">Akses Global</div>
                <div class="node-pill node-all" draggable="true" data-id="all" data-type="all">
                    <i class="fas fa-globe-asia opacity-50"></i> Semua Anggota
                </div>
            </div>

            <div class="mb-4">
                <div class="palette-group-title">Akses Per Divisi</div>
                <div class="d-flex flex-wrap">
                    @foreach($divisions as $div)
                    <div class="node-pill node-div" draggable="true" data-id="div_{{ $div->id }}" data-type="division">
                        <i class="fas fa-building opacity-50"></i> {{ $div->name }}
                    </div>
                    @endforeach
                </div>
            </div>

            <div>
                <div class="palette-group-title">Akses Individu</div>
                <div class="d-flex flex-wrap">
                    @foreach($users as $u)
                    <div class="node-pill node-user" draggable="true" data-id="user_{{ $u->id }}" data-type="user">
                        <i class="fas fa-user opacity-50"></i> {{ $u->name }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Target Work Area -->
        <div>
            <div class="search-wrapper mb-4">
                <i class="fas fa-search"></i>
                <input type="text" id="searchUser" class="form-control" placeholder="Cari nama anggota atau role untuk mengatur izin chat...">
            </div>
            
            <div id="users-container">
                @foreach($users as $u)
                <div class="user-card user-row" data-name="{{ strtolower($u->name) }} {{ strtolower($u->role) }}">
                    <div class="user-header">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <h5 style="margin:0; font-weight:800; color:#1e293b;">{{ $u->name }}</h5>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge" style="background: rgba(148, 163, 184, 0.1); color: #64748b; font-weight:600;">ID: {{ $u->id }}</span>
                                    <span class="badge {{ $u->role === 'superadmin' || $u->role === 'admin' ? 'bg-primary' : 'bg-secondary' }}">{{ strtoupper($u->role) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="drop-zone" data-source="user_{{ $u->id }}">
                        <div class="drop-zone-placeholder">
                            <i class="fas fa-bullseye"></i>
                            <span>Tarik (Drag) Izin ke Zona Ini</span>
                        </div>
                        <!-- Rendered nodes will append here -->
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<form id="saveForm" action="{{ route('admin.erp.chat_relations.save') }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="connections" id="connectionsData">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pills = document.querySelectorAll('.node-pill');
        const dropZones = document.querySelectorAll('.drop-zone');
        let draggedItem = null;

        // Data awal dari Server
        const initialConnections = @json($connections);
        
        function removeNode(btn) {
            btn.parentElement.remove();
        }

        // Render initial connections
        initialConnections.forEach(conn => {
            const dropZone = document.querySelector(`.drop-zone[data-source="${conn.source}"]`);
            if (dropZone) {
                const originalPill = document.querySelector(`.node-pill[data-id="${conn.target}"]`);
                if (originalPill) {
                    const clone = originalPill.cloneNode(true);
                    clone.removeAttribute('draggable');
                    const closeBtn = document.createElement('button');
                    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    closeBtn.onclick = function() { clone.remove(); };
                    clone.appendChild(closeBtn);
                    
                    // Hilangkan teks placeholder
                    const placeholder = dropZone.querySelector('.drop-zone-placeholder');
                    if (placeholder) placeholder.style.display = 'none';

                    dropZone.appendChild(clone);
                }
            }
        });

        pills.forEach(pill => {
            pill.addEventListener('dragstart', function(e) {
                draggedItem = this;
                setTimeout(() => this.style.opacity = '0.5', 0);
            });

            pill.addEventListener('dragend', function() {
                setTimeout(() => this.style.opacity = '1', 0);
                draggedItem = null;
            });
        });

        dropZones.forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            zone.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                
                if (draggedItem) {
                    // Cek duplikasi
                    const existing = this.querySelectorAll(`.node-pill[data-id="${draggedItem.getAttribute('data-id')}"]`);
                    if (existing.length === 0) {
                        const clone = draggedItem.cloneNode(true);
                        clone.removeAttribute('draggable');
                        
                        // Tambah tombol hapus
                        const closeBtn = document.createElement('button');
                        closeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        closeBtn.onclick = function() { clone.remove(); };
                        clone.appendChild(closeBtn);

                        const placeholder = this.querySelector('.drop-zone-placeholder');
                        if (placeholder) placeholder.style.display = 'none';

                        this.appendChild(clone);
                    }
                }
            });
        });

        // Search Filter
        document.getElementById('searchUser').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.user-row').forEach(row => {
                if (row.getAttribute('data-name').includes(term)) {
                    row.style.display = 'block';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    function saveConnections() {
        const dropZones = document.querySelectorAll('.drop-zone');
        const connections = [];

        dropZones.forEach(zone => {
            const source = zone.getAttribute('data-source');
            const pills = zone.querySelectorAll('.node-pill');
            
            pills.forEach(pill => {
                const target = pill.getAttribute('data-id');
                connections.push({ source: source, target: target });
            });
        });

        document.getElementById('connectionsData').value = JSON.stringify(connections);
        document.getElementById('saveForm').submit();
    }
</script>
@endsection
