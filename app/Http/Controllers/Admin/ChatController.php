<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Events\PusherBroadcast;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Cparticipant;
use App\Models\Message;
use App\Models\Admin;
use App\Models\Room;
use App\Models\User;

class ChatController extends Controller
{
    public function index(Request $request) {

        $authUser = Auth::user();

        $admin = admin();
        $roomId = session('roomId');

        
        // Retrieve all conversations for the room
        $authConverIds = Cparticipant::where('admin_id', $admin->id)->pluck('conv_id');
        $conversations = Conversation::with(['participants.user', 'participants.admin', 'messages'])
            ->whereIn('id', $authConverIds)
            ->where('room_id', $roomId)
            ->get();

        // dd($conversations);
        
        // Format conversation data for view
        $conversationData = $conversations->map(function($conversation) use ($admin) {

            $participant = $conversation->participants->firstWhere('admin_id', '!=', $admin->id);

            $name = $conversation->is_group ? $conversation->name : $participant->user->name ?? $participant->admin->name;
            $profile = $conversation->is_group ? '' : ($participant->user->profile ?? '');

            return [
                'id' => $conversation->id,
                'name' => $name,
                'profile' => $profile,
                'status' => 1,
            ];
        });

        
        $data['participants'] = $conversationData;
        // return $conversationData;
        // dd($data['participants']);

        return view('admin.pages.chat.index', $data);
    }

    public function chatContent(Request $request) {
        
        $auth = admin();
        $convId = decrypt($request->convId);

        $conversation = Conversation::where('id', $convId)->where('is_group', 1)->first();
        if ($conversation && $conversation->is_group == TRUE) {
            $isGroup = TRUE;
        } else {
            $isGroup = FALSE;
        }

        $messages = Message::with(['user', 'admin'])->where('conv_id', $convId)->get()->toArray();

        return  response()->json([
            'data' => encrypt($convId),
            'messages' => $messages,
            'auth' => $auth->id,
            'isGroup' => $isGroup,
            'convId' => $convId,
        ], 200);
    }

    public function broadcast(Request $request) {

        $request->validate([
            'convId' => 'required',
        ]);
        
        $admin = admin();
        $convId = decrypt($request->convId);
        $message = Message::create([
            'conv_id' => $convId,
            'admin_id' => $admin->id,
            'message' => $request->message
        ]);
        
        $time = $message->created_at->format('g:i A');
        $conversation = Conversation::where('id', $convId)->where('is_group', 1)->first();
        
        if ($conversation && $conversation->is_group == TRUE) {
            $isGroup = TRUE;
        } else {
            $isGroup = FALSE;
        }

        broadcast(new PusherBroadcast($request->get('message'), $admin->name, $isGroup, $convId))->toOthers();

        return response()->json([
            'message' => $request->message,
            'time' => $time,
        ]);
    }

    public function receive(Request $request) {

        $date = Carbon::now();
        $time = Carbon::createFromFormat('Y-m-d H:i:s', $date)
                        ->format('g:i A');

        return response()->json([
            'message' => $request->message,
            'username' => $request->username,
            'isGroup' => $request->isGroup,
            'convId' => $request->convId,
            'time' => $time,
        ]);
    }
}
