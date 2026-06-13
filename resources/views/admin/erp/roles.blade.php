@extends('layouts.admin')

@section('title', 'Role Management - ERP')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.erp.index') }}" class="btn btn-outline mb-2" style="border-radius:50px; font-weight:600; font-size:13px;">&larr; Kembali ke ERP</a>
        <h1 style="font-weight: 800; color: var(--primary);">Hak Akses & Perizinan</h1>
        <p class="text-muted m-0">Tabel matriks untuk mengatur seluruh hak akses anggota (kecuali Superadmin).</p>
    </div>
    <button type="submit" form="rolesForm" class="btn btn-primary" style="border-radius:50px; font-weight:700; padding:10px 24px; box-shadow:0 4px 15px rgba(var(--primary-rgb, 99, 102, 241), 0.3);">
        💾 Simpan Perubahan
    </button>
</div>

@if(session('success'))
<div class="alert alert-success" style="border-radius:12px; font-weight:600; border:none; background:rgba(34,197,94,0.1); color:#16a34a;">
    {{ session('success') }}
</div>
@endif

<div class="card p-0" style="border-radius:24px; border:none; box-shadow:0 10px 30px rgba(0,0,0,0.03); overflow:hidden;">
    <form id="rolesForm" action="{{ route('admin.erp.roles.update') }}" method="POST">
        @csrf
        <div class="table-responsive" style="max-height: 70vh;">
            <table class="table table-hover align-middle mb-0" style="min-width:1500px; font-size:13px;">
                <thead style="background:#f8fafc; position:sticky; top:0; z-index:10;">
                    <tr>
                        <th style="padding:15px; border-bottom:2px solid #e2e8f0; position:sticky; left:0; background:#f8fafc; z-index:11;">Anggota</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0; background:#eff6ff;">Admin<br><small>Sistem</small></th>
                        <th style="padding:15px; border-bottom:2px solid #e2e8f0; background:#f0fdf4;">Penempatan<br>Divisi</th>
                        <th style="padding:15px; border-bottom:2px solid #e2e8f0; background:#f0fdf4;">Akses Laporan<br>Keuangan</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0; background:#fef3c7;">Kelola<br>Anggota</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0; background:#fef3c7;">Kelola<br>Keuangan</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0; background:#fef3c7;">Alokasi<br>Anggaran</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0; background:#fef3c7;">Kelola<br>Limbah</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0; background:#fef3c7;">Kelola<br>Komunitas</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0;">Akses<br>Chat</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0;">Akses<br>Komen</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0;">Akses<br>Posting</th>
                        <th class="text-center" style="padding:15px; border-bottom:2px solid #e2e8f0;">Input<br>Limbah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <!-- Anggota (Sticky Column) -->
                        <td style="padding:12px 15px; border-bottom:1px solid #f1f5f9; position:sticky; left:0; background:white; box-shadow:2px 0 5px rgba(0,0,0,0.02);">
                            <div style="font-weight:700; color:#1e293b; font-size:14px;">{{ $member->name }}</div>
                            <div style="font-size:11px; color:#64748b;">{{ $member->email }}</div>
                        </td>
                        
                        <!-- Role Sistem (Admin) -->
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(239,246,255,0.3);">
                            <div class="form-check form-switch d-flex justify-content-center m-0">
                                <input class="form-check-input" type="checkbox" name="permissions[{{ $member->id }}][admin]" value="1" {{ $member->role === 'admin' ? 'checked' : '' }} style="cursor:pointer;">
                            </div>
                        </td>

                        <!-- Divisi -->
                        <td style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(240,253,244,0.3);">
                            <select name="permissions[{{ $member->id }}][division_id]" class="form-select form-select-sm" style="border-radius:8px; font-weight:600; width:150px;">
                                <option value="">- Tidak Ada -</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ $member->division_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </td>

                        <!-- Finance View Scope -->
                        <td style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(240,253,244,0.3);">
                            <select name="permissions[{{ $member->id }}][finance_view_scope]" class="form-select form-select-sm" style="border-radius:8px; width:140px;">
                                <option value="none" {{ $member->finance_view_scope === 'none' ? 'selected' : '' }}>Tidak Boleh</option>
                                <option value="division" {{ $member->finance_view_scope === 'division' ? 'selected' : '' }}>Hanya Divisi</option>
                                <option value="global" {{ $member->finance_view_scope === 'global' ? 'selected' : '' }}>Semua (Global)</option>
                            </select>
                        </td>

                        <!-- Admin Pendamping -->
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(254,243,199,0.2);">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_manage_members]" value="1" {{ $member->can_manage_members ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(254,243,199,0.2);">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_manage_finances]" value="1" {{ $member->can_manage_finances ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(254,243,199,0.2);">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_allocate_budgets]" value="1" {{ $member->can_allocate_budgets ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(254,243,199,0.2);">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_manage_waste]" value="1" {{ $member->can_manage_waste ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9; background:rgba(254,243,199,0.2);">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_manage_posts]" value="1" {{ $member->can_manage_posts ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>

                        <!-- Izin Biasa -->
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9;">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_chat]" value="1" {{ ($member->can_chat ?? 1) ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9;">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_comment]" value="1" {{ ($member->can_comment ?? 1) ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9;">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_post]" value="1" {{ ($member->can_post ?? 1) ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                        <td class="text-center" style="padding:12px 15px; border-bottom:1px solid #f1f5f9;">
                            <input type="checkbox" name="permissions[{{ $member->id }}][can_input_waste]" value="1" {{ ($member->can_input_waste ?? 1) ? 'checked' : '' }} style="width:16px; height:16px; cursor:pointer;">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection
