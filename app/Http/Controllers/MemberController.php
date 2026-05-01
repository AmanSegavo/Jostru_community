<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Event;
use App\Models\WasteDeposit;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MemberController extends Controller
{
    public function dashboard()
    {
        try {
            $totalWeight = WasteDeposit::where('user_id', auth()->id())
                ->where('status', 'APPROVED')
                ->sum('weight');
        } catch (\Exception $e) {
            $totalWeight = 0;
        }

        try {
            $nextEvent = Event::where('event_date', '>=', now())
                ->orderBy('event_date', 'asc')
                ->first();
        } catch (\Exception $e) {
            $nextEvent = null;
        }

        return view('member.dashboard', compact('totalWeight', 'nextEvent'));
    }

    public function feed()
    {
        try {
            $posts = Post::with(['user', 'comments.user'])->latest()->paginate(10);
        } catch (\Exception $e) {
            $posts = collect();
        }
        return view('member.feed', compact('posts'));
    }

    public function events()
    {
        try {
            $events = Event::orderBy('event_date', 'asc')->get();
        } catch (\Exception $e) {
            $events = collect();
        }
        return view('member.events', compact('events'));
    }

    public function wasteReport()
    {
        try {
            $reports = WasteDeposit::where('user_id', auth()->id())->latest()->get();
        } catch (\Exception $e) {
            $reports = collect();
        }
        return view('member.waste_report', compact('reports'));
    }

    public function storeWasteReport(Request $request)
    {
        $request->validate([
            'type'        => 'required',
            'weight'      => 'required|numeric|min:0.1',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('waste_reports', 'public');
        }

        WasteDeposit::create([
            'user_id'     => auth()->id(),
            'type'        => $request->type,
            'weight'      => $request->weight,
            'description' => $request->description,
            'image_path'  => $imagePath
        ]);

        // --- HYBRID ARCHITECTURE: Kirim data ke Python API (Opsional) ---
        try {
            $pythonApiUrl = env('PYTHON_API_URL', 'http://localhost:8000/api/v1/waste/deposits');
            $pythonApiKey = env('PYTHON_API_KEY', 'rahasia-super-jostru-123');

            \Illuminate\Support\Facades\Http::timeout(3)->withHeaders([
                'X-API-Key' => $pythonApiKey
            ])->post($pythonApiUrl, [
                'user_id'   => auth()->id(),
                'category'  => $request->type,
                'weight_kg' => (float) $request->weight
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Python API Offline: ' . $e->getMessage());
        }

        return back()->with('success', 'Laporan setoran limbah berhasil dikirim dan menunggu persetujuan admin.');
    }

    // --- Chat & Call Features ---
    public function chatList()
    {
        // Aman: cek kolom can_chat dulu sebelum diakses
        $user = auth()->user();
        $canChat = true;
        try {
            if (Schema::hasColumn('users', 'can_chat')) {
                $canChat = (bool) $user->can_chat;
            }
        } catch (\Exception $e) {
            $canChat = true;
        }

        if (!$canChat) {
            return back()->with('error', 'Izin komunikasi Anda dinonaktifkan oleh Admin.');
        }

        try {
            $users = User::where('id', '!=', auth()->id())->get();
        } catch (\Exception $e) {
            $users = collect();
        }

        return view('member.chat_list', compact('users'));
    }

    public function chatRoom($receiverId)
    {
        $receiver = User::findOrFail($receiverId);

        // Aman: cek kolom can_chat dulu
        try {
            if (Schema::hasColumn('users', 'can_chat')) {
                if (!auth()->user()->can_chat || !$receiver->can_chat) {
                    return redirect()->route('member.chat.list')
                        ->with('error', 'Komunikasi antar anggota sedang dibatasi.');
                }
            }
        } catch (\Exception $e) {
            // Jika kolom belum ada, izinkan chat
        }

        try {
            $messages = Chat::where(function ($q) use ($receiverId) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', auth()->id());
            })->orderBy('created_at', 'asc')->get();

            // Mark as read
            Chat::where('sender_id', $receiverId)
                ->where('receiver_id', auth()->id())
                ->update(['is_read' => true]);
        } catch (\Exception $e) {
            $messages = collect();
        }

        return view('member.chat_room', compact('receiver', 'messages'));
    }

    public function sendMessage(Request $request, $receiverId)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        try {
            $chat = Chat::create([
                'sender_id'   => auth()->id(),
                'receiver_id' => $receiverId,
                'message'     => $request->message
            ]);

            return response()->json([
                'success'    => true,
                'id'         => $chat->id,
                'message'    => $chat->message,
                'sender_id'  => $chat->sender_id,
                'created_at' => $chat->created_at->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --- Polling endpoint untuk real-time chat ---
    public function pollMessages(Request $request, $receiverId)
    {
        try {
            $afterId = (int) $request->get('after', 0);

            $messages = Chat::where(function ($q) use ($receiverId) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $receiverId);
            })->orWhere(function ($q) use ($receiverId) {
                $q->where('sender_id', $receiverId)->where('receiver_id', auth()->id());
            })->where('id', '>', $afterId)
              ->orderBy('created_at', 'asc')
              ->get(['id', 'sender_id', 'message', 'created_at']);

            // Mark as read
            Chat::where('sender_id', $receiverId)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function videoCall($receiverId)
    {
        $receiver = User::findOrFail($receiverId);
        $ids = [auth()->id(), $receiverId];
        sort($ids);
        $roomName = "JostruCall_" . implode("_", $ids);

        return view('member.video_call', compact('receiver', 'roomName'));
    }

    // --- Kartu Digital Member ---
    public function myCardEditor()
    {
        $user = auth()->user();
        return view('member.card_editor', compact('user'));
    }

    public function downloadMyCard(Request $request)
    {
        $ctrl = new AdminController();
        return $ctrl->generateCardCustom($request, auth()->id());
    }
}
