<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Events\PusherBroadcast;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Cparticipant;
use Illuminate\Support\Arr;
use App\Models\Message;
use App\Models\Admin;
use App\Models\Room;
use App\Models\User;

class ChatController extends Controller
{
    public function index(Request $request) {

        $authUser = Auth::user();

        $authConverIds = Cparticipant::where('staff_id', $authUser->id)->get()->pluck('conv_id');
        $conversations = Conversation::with(['participants.user', 'participants.admin', 'messages'])
            ->where('room_id', $authUser->room_id)
            ->whereIn('id', $authConverIds)
            ->get();

        // dd($conversations);

        // Get user IDs from one-on-one conversations
        $userIdsIn = $conversations->filter(function($conversation) use ($authUser) {
            return !$conversation->is_group;
        })->pluck('participants.*.staff_id')->flatten()->unique()->toArray();


        $userIdsIn = Arr::where($userIdsIn, function ($value) {
            return !is_null($value);
        });
        
        // Get users in the same room who are not in any one-on-one conversation
        $usersNotIn = User::where('room_id', $authUser->room_id)
            ->where('id', '!=', $authUser->id)
            ->whereNotIn('id', $userIdsIn)
            ->get();
        
        // dd($usersNotIn);
        
        // Format conversation data for view
        $conversationData = $conversations->map(function($conversation) use ($authUser) {
            $participant = $conversation->participants->firstWhere('staff_id', '!=', $authUser->id);

            $name = $conversation->is_group ? $conversation->name : $participant->user->name ?? $participant->admin->name;
            $profile = $conversation->is_group ? '' : ($participant->user->profile ?? '');

            return [
                'id' => $conversation->id,
                'name' => $name,
                'profile' => $profile,
                'status' => 1,
            ];
        });

        // return $conversationData;
        // dd($conversationData);

        $userData = $usersNotIn->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'profile' => $user->profile ?? '',
                'status' => 0
            ];
        });

        $data['participants'] = $conversationData->merge($userData);
        // return $data['participants'];

        return view('staff.pages.chat.index', $data);
    }

    public function broadcast(Request $request) {

        $request->validate([
            'convId' => 'required',
        ]);

        $convId = decrypt($request->convId);
        $message = Message::create([
            'conv_id' => $convId,
            'staff_id' => Auth::id(),
            'message' => $request->message
        ]);
        
        $time = $message->created_at->format('g:i A');

        $conversation = Conversation::where('id', $convId)->where('is_group', 1)->first();
        if ($conversation && $conversation->is_group == TRUE) {
            $isGroup = TRUE;
        } else {
            $isGroup = FALSE;
        }

        broadcast(new PusherBroadcast($request->get('message'), Auth::user()->name, $isGroup, $convId))->toOthers();

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

    public function chatContent(Request $request) {
        
        $auth = Auth::user();
        if( $request->convId ) {
            $convId = decrypt($request->convId);
        } else {
            $userId = decrypt($request->userId);
            $conv = Conversation::create([ 'name' => null, 'room_id' => $auth->room_id ]);
            Cparticipant::create([ 'conv_id' => $conv->id, 'staff_id' => $auth->id ]);
            Cparticipant::create([ 'conv_id' => $conv->id, 'staff_id' => $userId ]);
            $convId = $conv->id;
        }

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
}
