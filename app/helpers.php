<?php

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