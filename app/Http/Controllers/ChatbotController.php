<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatbotService;
use App\Models\DataLakeRecord;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userMessage = $request->message;
        $botResponse = $this->chatbotService->getResponse($userMessage);

        // Simpan log ke Data Lake
        DataLakeRecord::create([
            'division_id' => null, // Global
            'category' => 'CHATBOT_LOG',
            'payload' => [
                'user_id' => Auth::id() ?? 0,
                'user_name' => Auth::user()->name ?? 'Guest',
                'question' => $userMessage,
                'answer' => $botResponse,
                'llm_provider' => Setting::getVal('llm_provider', 'rule_based')
            ],
            'status' => 'PROCESSED',
            'created_by' => Auth::id() ?? 1
        ]);

        return response()->json([
            'success' => true,
            'reply' => $botResponse
        ]);
    }
}
