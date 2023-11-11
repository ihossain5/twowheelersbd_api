<?php

namespace App\Services;

use App\Models\Message;

Class MessageService {

    public function messagesById($id){
        $messages = Message::query()
        ->where('chat_id',$id)
        ->get();

        return $messages;
    }

}