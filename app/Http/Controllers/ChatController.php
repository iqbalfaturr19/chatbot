<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChatSession;
use App\ChatHistory;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function startChat() {
        $sessionId = uniqid('chat_', true);
        return response()->json(['session_id' => $sessionId]);
    }

    public function chat(Request $request) {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'session_id' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $sessionId = $request->session_id;
        $userMessage = $request->message;

        $session = ChatSession::firstOrCreate(
            ['session_id' => $sessionId],
            ['title' => $userMessage]
        );
        $history = ChatHistory::where('session_id', $sessionId)
        ->orderBy('created_at')
        ->get(['user_message', 'bot_response']);

        $messages = [];
        foreach ($history as $chat) {
            $messages[] = ['role' => 'user', 'parts' => [['text' => $chat->user_message]]];
            $messages[] = ['role' => 'model', 'parts' => [['text' => $chat->bot_response]]];
        }

        $messages[] = ['role' => 'user', 'parts' => [['text' => $userMessage]]];

        $apiKey = env('GEMINI_API_KEY');
        $client = new Client();
        try {
            $response = $client->post("https://generativelanguage.googleapis.com/v1/models/gemini-1.0-pro:generateContent?key={$apiKey}", [
                'json' => ['contents' => $messages]
            ]);    
            
            $data = json_decode($response->getBody(), true);
    
            if (!empty($data['candidates'][0]['content']['parts'][0]['text'])) {
                $botResponse = $data['candidates'][0]['content']['parts'][0]['text'];
            } else {
                $botResponse = 'Maaf, saya tidak mengerti.';
            }
    
        } catch (\Exception $e) {
            \Log::error('Error saat menghubungi API Gemini: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
        ChatHistory::create([
            'session_id' => $sessionId,
            'user_message' => $userMessage,
            'bot_response' => $botResponse
        ]);

        return response()->json(['response' => $botResponse]);
    }

    public function history($sessionId) {
        $history = ChatHistory::where('session_id', $sessionId)->get();
        return response()->json($history);
    }

    public function getSessions() {
        $sessions = ChatSession::orderBy('created_at', 'desc')->get();
        return response()->json($sessions);
    }

    public function deleteSession(Request $request)
    {
        $sessionId = $request->session_id;

        if (!$sessionId) {
            return response()->json(['error' => 'Session ID diperlukan'], 400);
        }

        ChatSession::where('session_id', $sessionId)->delete();
        ChatHistory::where('session_id', $sessionId)->delete();

        return response()->json(['success' => 'Session berhasil dihapus']);
    }
}
