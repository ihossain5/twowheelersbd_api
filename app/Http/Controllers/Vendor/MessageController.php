<?php

namespace App\Http\Controllers\vendor;

use App\Events\UserMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\ShopOwner;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public $owner_id;

    public function __construct() {
        $this->owner_id = auth('vendor')->user()?->id;
    }

    public function allMessages(){
        $messages = Chat::query()->with('user')->where('shop_owner_id',$this->owner_id)->latest()->get();

        return $this->success(ChatResource::collection($messages));
    }

    public function getMessageById($id, MessageService $message){
        $messages = $message->messagesById($id);

        return $this->success(MessageResource::collection($messages));
    }

    public function sendMessage(Request $request){
        $this->validate($request,['user_id'=> 'required', 'message' => 'required' ]);
        
        $vendor = ShopOwner::find($this->owner_id); 

        event(new UserMessage($request->message,$request->user_id, $vendor->shop));

        $chat_exists = Chat::query()->select('id')->where('user_id',$request->user_id)->where('shop_owner_id',$vendor->id)->first();

        if($chat_exists){
            $chat = $chat_exists;
        }else{
            $chat = new Chat();
        }

        $chat->user_id = $request->user_id;
        $chat->shop_owner_id = $vendor->id;
        $chat->save();

        $message = new Message();
        $message->chat_id = $chat->id;
        $message->send_by = 'OWNER';
        $message->message = $request->message;
        $message->save();

        return $this->success(new MessageResource($message));
    }
}
