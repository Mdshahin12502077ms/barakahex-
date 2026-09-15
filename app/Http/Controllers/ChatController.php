<?php

namespace App\Http\Controllers;

use App\Events\ChatEvent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
   
   public function index()
{
    $authId = Auth::id();

   
    $users = User::role('service_provider')->get();


    $unreadCounts = Chat::select('sender_id', DB::raw('COUNT(*) as unread'))
        ->where('receiver_id', $authId)
        ->where('is_read', 0)
        ->groupBy('sender_id')
        ->pluck('unread', 'sender_id');

    return view('backend.layouts.Chats.index', compact('users', 'unreadCounts'));
}


    // conversation_id generate (same logic as Api\ChatController)
    protected function makeConversationId($userA, $userB)
    {
        $a = (int) $userA;
        $b = (int) $userB;

        return min($a, $b) . '-' . max($a, $b);
    }

  
    public function fetchConversation($receiverId)
    {
        try {
            $authId = Auth::id();
            $conversationId = $this->makeConversationId($authId, $receiverId);

            $messages = Chat::where('conversation_id', $conversationId)
                ->orderBy('created_at', 'asc')
                ->with('chatimage')
                ->get();

            $messages->each(function ($msg) {
                // IMAGE
                if ($msg->chatimage) {
                    $msg->image_url = asset($msg->chatimage->image);
                    $msg->image_id  = $msg->chatimage->id;
                } else {
                    $msg->image_url = null;
                    $msg->image_id  = null;
                }

                // FILE (chat table এর file কলাম)
                if ($msg->file) {
                    $msg->file_url = asset($msg->file); // path public/ এর ভিতরে
                } else {
                    $msg->file_url = null;
                }

                // nice formatted time চাইলে:
                $msg->time = Carbon::parse($msg->created_at)->format('d M Y, h:i A');
            });

            return response()->json([
                'status'          => true,
                'chat'            => $messages,
                'conversation_id' => $conversationId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // MESSAGE SEND (backend AJAX থেকে)
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $authId     = Auth::id();
            $receiverId = $request->receiver_id;
            $message    = $request->message;

            // কমপক্ষে message বা file/image একটা থাকতে হবে
            if (!$message && !$request->hasFile('file') && !$request->hasFile('image')) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Message or file/image is required.',
                ], 422);
            }

            $conversationId = $this->makeConversationId($authId, $receiverId);

            $chat = new Chat();
            $chat->sender_id       = $authId;
            $chat->receiver_id     = $receiverId;
            $chat->message         = $message;
            $chat->conversation_id = $conversationId;

            // FILE UPLOAD (chat table এর file কলাম)
            if ($request->hasFile('file')) {
                $file      = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                $fileName  = time() . '_file.' . $extension;
                $path      = 'uploads/chat_files/';

                $file->move(public_path($path), $fileName);
                $chat->file = $path . $fileName;
            }

            $chat->save();

            // IMAGE UPLOAD (chat_images table)
            if ($request->hasFile('image')) {
                $image      = $request->file('image');
                $extension  = $image->getClientOriginalExtension();
                $imageName  = time() . '_img.' . $extension;
                $path       = 'uploads/chat_images/';

                $image->move(public_path($path), $imageName);

                $chatImage = new ChatImage();
                $chatImage->chat_id = $chat->id;
                $chatImage->image   = $path . $imageName;
                $chatImage->save();
            }

         
            $chat->load('chatimage');

           
            $chat->image_url = $chat->chatimage ? asset($chat->chatimage->image) : null;
            $chat->file_url  = $chat->file ? asset($chat->file) : null;

          
            broadcast(new ChatEvent($chat))->toOthers();

            return response()->json([
                'status'  => true,
                'message' => 'Message sent successfully',
                'data'    => $chat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // MARK AS READ (backend)
    public function markAsRead($receiverId)
    {
        try {
            $authId        = Auth::id();
            $conversationId = $this->makeConversationId($authId, $receiverId);

            Chat::where('conversation_id', $conversationId)
                ->where('receiver_id', $authId)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            return response()->json([
                'status'  => true,
                'message' => 'Messages marked as read',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // MESSAGE DELETE
    public function chatDelete($id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $chat->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Chat deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // CHAT IMAGE DELETE
    public function chatImageDelete($id)
    {
        try {
            $chatImage = ChatImage::findOrFail($id);
            $chatImage->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Chat Image deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
