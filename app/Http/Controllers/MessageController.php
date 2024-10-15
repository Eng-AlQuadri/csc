<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\PusherBroadcast;
use Pusher\Pusher;

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

    public function getGroupMessages($groupId)
    {

        $userId = Auth::id(); // Get the current authenticated user ID

        // Fetch messages associated with the specified group ID
        $messages = Message::where(function ($query) use ($groupId, $userId) {
            $query->where('group_id', $groupId) // Group messages
                ->orWhere(function ($q) use ($userId, $groupId) {
                    $q->where('sender_id', $userId) // Sender messages
                        ->where('group_id', $groupId);
                });
        })
            ->orderBy('created_at') // Order messages by creation time
            ->get();

        return response()->json($messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request)
    {
        $data = $request->validated();

        $message = Message::create($data);

        broadcast(new MessageEvent($message))->toOthers();

        return response()->json($data, 201);
    }

    public function getUsersWithSharedSubjects()
    {
        $currentUser = Auth::user();  // Get the authenticated user

        // Get shared users with their subjects
        $sharedUsers = User::whereHas('subjects', function ($query) use ($currentUser) {
            $query->whereIn('subject_id', $currentUser->subjects->pluck('id'));
        })
            ->where('id', '!=', $currentUser->id) // Exclude current user
            ->with(['subjects' => function ($query) {
                $query->select('subjects.id', 'subjects.name'); // Load subject details
            }])
            ->get();

        // Get all admins
        $admins = User::where('role', 'admin')->get();

        $combinedResults = $sharedUsers->merge($admins);

        return response()->json($combinedResults);
    }
}
