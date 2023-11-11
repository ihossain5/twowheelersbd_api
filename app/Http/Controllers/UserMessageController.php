<?php

namespace App\Http\Controllers;

use App\Events\VendorMessage;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Shop;
use App\Models\User;
use App\Services\ImageUoloadService;
use App\Services\MessageService;
use Illuminate\Http\Request;

class UserMessageController extends Controller
{
    public $user_id;

    public function __construct() {
        $this->user_id = auth()->user()?->id;
    }

    public function allMessages(){
        $messages = Chat::query()->with('owner')->where('user_id',$this->user_id)->latest()->get();

        return $this->success(ChatResource::collection($messages));
    }

    public function getMessageById($id, MessageService $message){
        $messages = $message->messagesById($id);

        return $this->success(MessageResource::collection($messages));
    }

    public function sendMessage(Request $request){
        $this->validate($request,['shop_id'=> 'required', 'message' => 'required' ]);
        $image = null;
        if($request->image){
            $image = ( new ImageUoloadService())->storeImage($request->image,'chat/',800,800);
        }

        $shop = Shop::select('shop_owner_id')->find($request->shop_id);

        $chat_exists = Chat::query()->select('id')->where('user_id',$this->user_id)->where('shop_owner_id',$shop->shop_owner_id)->first();

        if($chat_exists){
            $chat = $chat_exists;
        }else{
            $chat = new Chat();
        }

        $chat->user_id = $this->user_id;
        $chat->shop_owner_id = $shop->shop_owner_id;
        $chat->save();

        $message = new Message();
        $message->chat_id = $chat->id;
        $message->send_by = 'USER';
        $message->message = $request->message;
        $message->image = $image;
        $message->save();

        $user = User::find($this->user_id); 

        event(new VendorMessage($request->message,$shop->shop_owner_id, $user, $chat->id, $image));

        return $this->success(new MessageResource($message));
    }
}
