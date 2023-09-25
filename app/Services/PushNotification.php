<?php

namespace App\Services;

Class PushNotification {

     function sendToOne($to, $title, $message, $image = ''){
        $data = [
            "notification" => [
                  "title" => $title, 
                  "body" => $message, 
                  "icon" => $image
               ], 
            "to" => $to, 
            "priority" => "high" 
         ]; 

         return $this->sendRequest($data);
    }


    private function sendRequest($data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headersArr());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = curl_exec($ch);
        curl_close( $ch );

        return $result;
    }

    private function headersArr(){
        return array(
            'Authorization: key='.config('app.fcm_push_app_key'),
            'Content-Type: application/json'
        );
    }
}