<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Fetch all messages between the current user and the selected contact
    public function index($receiverId)
    {
        $userId = Auth::id(); // Get current authenticated user ID

        // Get messages where the current user is either the sender or receiver
        $messages = Message::where(function ($query) use ($userId, $receiverId) {

            $query->where('sender_id', $userId)

                ->where('reciever_id', $receiverId);
        })->orWhere(function ($query) use ($userId, $receiverId) {

            $query->where('sender_id', $receiverId)

                ->where('reciever_id', $userId);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();

        Message::create($data);

        return response()->json('', 201);
    }
}
