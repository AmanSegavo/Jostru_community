<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;

class ERPController extends Controller
{
    public function index()
    {
        return view('admin.erp.index');
    }

    public function roles()
    {
        // Ambil semua user kecuali superadmin yang absolut
        $members = User::where('role', '!=', 'superadmin')->with('division')->get();
        $divisions = Division::all();
        return view('admin.erp.roles', compact('members', 'divisions'));
    }

    public function updateRoles(Request $request)
    {
        $permissions = $request->input('permissions', []);
        
        // Dapatkan semua ID user yang bukan superadmin
        $userIds = User::where('role', '!=', 'superadmin')->pluck('id')->toArray();
        
        $hasCanChat = \Illuminate\Support\Facades\Schema::hasColumn('users', 'can_chat');
        $hasCanComment = \Illuminate\Support\Facades\Schema::hasColumn('users', 'can_comment');
        $hasCanPost = \Illuminate\Support\Facades\Schema::hasColumn('users', 'can_post');
        $hasCanInputWaste = \Illuminate\Support\Facades\Schema::hasColumn('users', 'can_input_waste');
        
        foreach ($userIds as $userId) {
            $userPerms = $permissions[$userId] ?? [];
            
            $data = [
                'role' => isset($userPerms['admin']) ? 'admin' : 'member',
                'division_id' => $userPerms['division_id'] ?? null,
                'finance_view_scope' => $userPerms['finance_view_scope'] ?? 'none',
                'can_manage_members' => isset($userPerms['can_manage_members']) ? 1 : 0,
                'can_manage_finances' => isset($userPerms['can_manage_finances']) ? 1 : 0,
                'can_allocate_budgets' => isset($userPerms['can_allocate_budgets']) ? 1 : 0,
                'can_manage_waste' => isset($userPerms['can_manage_waste']) ? 1 : 0,
                'can_manage_posts' => isset($userPerms['can_manage_posts']) ? 1 : 0,
            ];

            if ($hasCanChat) $data['can_chat'] = isset($userPerms['can_chat']) ? 1 : 0;
            if ($hasCanComment) $data['can_comment'] = isset($userPerms['can_comment']) ? 1 : 0;
            if ($hasCanPost) $data['can_post'] = isset($userPerms['can_post']) ? 1 : 0;
            if ($hasCanInputWaste) $data['can_input_waste'] = isset($userPerms['can_input_waste']) ? 1 : 0;
            
            User::where('id', $userId)->update($data);
        }
        
        return redirect()->route('admin.erp.roles')->with('success', 'Hak Akses & Perizinan berhasil diperbarui secara massal.');
    }

    public function chatRelations()
    {
        $users = \App\Models\User::all(['id', 'name', 'role']);
        $divisions = \App\Models\Division::all(['id', 'name']);
        
        $relations = \App\Models\ChatRelation::all();

        // Format data untuk frontend (node graph)
        // Kita butuh format connections: [ { source: 'user_1', target: 'user_2' }, { source: 'user_1', target: 'div_1' } ]
        $connections = [];
        foreach($relations as $rel) {
            $connections[] = [
                'source' => 'user_' . $rel->source_user_id,
                'target' => $rel->target_type === 'all' ? 'all' : ($rel->target_type === 'division' ? 'div_' . $rel->target_id : 'user_' . $rel->target_id)
            ];
        }

        return view('admin.erp.chat_relations', compact('users', 'divisions', 'connections'));
    }

    public function saveChatRelations(Request $request)
    {
        // $request->connections is a JSON array: [{"source": "user_1", "target": "user_2"}, ...]
        $connectionsJson = $request->input('connections');
        $connections = json_decode($connectionsJson, true);

        if (!is_array($connections)) {
            return back()->with('error', 'Format data tidak valid.');
        }

        // Hapus semua relasi lama untuk direset dengan yang baru
        \App\Models\ChatRelation::truncate();

        foreach($connections as $conn) {
            $sourceStr = $conn['source']; // 'user_1'
            $targetStr = $conn['target']; // 'user_2' or 'div_1' or 'all'

            if (!str_starts_with($sourceStr, 'user_')) continue;
            $sourceId = str_replace('user_', '', $sourceStr);

            $targetType = 'user';
            $targetId = null;

            if ($targetStr === 'all') {
                $targetType = 'all';
            } elseif (str_starts_with($targetStr, 'div_')) {
                $targetType = 'division';
                $targetId = str_replace('div_', '', $targetStr);
            } elseif (str_starts_with($targetStr, 'user_')) {
                $targetType = 'user';
                $targetId = str_replace('user_', '', $targetStr);
            }

            \App\Models\ChatRelation::create([
                'source_user_id' => $sourceId,
                'target_type' => $targetType,
                'target_id' => $targetId
            ]);
        }

        return back()->with('success', 'Relasi chat berhasil disimpan dan diperbarui.');
    }

    public function tools()
    {
        return view('admin.erp.tools');
    }
}
