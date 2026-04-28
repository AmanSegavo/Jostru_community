<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Event;
use App\Models\WasteDeposit;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function dashboard()
    {
        $totalWeight = WasteDeposit::where('user_id', auth()->id())
            ->where('status', 'APPROVED')
            ->sum('weight');
            
        $nextEvent = Event::where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->first();
            
        return view('member.dashboard', compact('totalWeight', 'nextEvent'));
    }

    public function feed()
    {
        $posts = Post::with(['user', 'comments.user'])->latest()->paginate(10);
        return view('member.feed', compact('posts'));
    }

    public function events()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('member.events', compact('events'));
    }

    public function wasteReport()
    {
        $reports = WasteDeposit::where('user_id', auth()->id())->latest()->get();
        return view('member.waste_report', compact('reports'));
    }

    public function storeWasteReport(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'weight' => 'required|numeric|min:0.1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('waste_reports', 'public');
        }

        WasteDeposit::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'weight' => $request->weight,
            'description' => $request->description,
            'image_path' => $imagePath
        ]);

        return back()->with('success', 'Laporan setoran limbah berhasil dikirim dan menunggu persetujuan admin.');
    }

    // --- Chat & Call Features ---
    public function chatList()
    {
        if (!auth()->user()->can_chat) {
            return back()->with('error', 'Izin komunikasi Anda dinonaktifkan oleh Admin.');
        }

        // Ambil daftar user lain (tim)
        $users = User::where('id', '!=', auth()->id())->get();
        return view('member.chat_list', compact('users'));
    }

    public function chatRoom($receiverId)
    {
        $receiver = User::findOrFail($receiverId);
        
        // Cek izin admin
        if (!auth()->user()->can_chat || !$receiver->can_chat) {
            return redirect()->route('member.chat.list')->with('error', 'Komunikasi antar anggota sedang dibatasi.');
        }

        $messages = Chat::where(function($q) use ($receiverId) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $receiverId);
        })->orWhere(function($q) use ($receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        // Mark as read
        Chat::where('sender_id', $receiverId)->where('receiver_id', auth()->id())->update(['is_read' => true]);

        return view('member.chat_room', compact('receiver', 'messages'));
    }

    public function sendMessage(Request $request, $receiverId)
    {
        $request->validate(['message' => 'required']);

        Chat::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $receiverId,
            'message' => $request->message
        ]);

        return response()->json(['success' => true]);
    }

    public function videoCall($receiverId)
    {
        $receiver = User::findOrFail($receiverId);
        // Room ID unik untuk kedua user
        $ids = [auth()->id(), $receiverId];
        sort($ids);
        $roomName = "JostruCall_" . implode("_", $ids);
        
        return view('member.video_call', compact('receiver', 'roomName'));
    }
}
