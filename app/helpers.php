<?php

use Carbon\Carbon;

function BASE_URL(){
    return 'http://127.0.0.1:8000/admin/images/';
}

function addUrl($images){
    $data = [];
    foreach ($images as $image){
        $data[] = BASE_URL().$image;
    }
    return $data;
}

function generateOtp(){
    return rand(1000, 9999);
}

function formatDate($date){
   return Carbon::parse($date)->format('F d, Y');
}

function getAverageRating($total_review, $total_sum){
    if($total_review == 0) return 0;
    return number_format($total_sum / $total_review, 1);
}