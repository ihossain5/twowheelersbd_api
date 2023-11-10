<?php

namespace App\Services;

use App\Models\ShopVideo;

class VideoService {
    private $model;

    public function __construct(ShopVideo $model) {
        $this->model = $model;
    }

    public function store($data, $shop_id, $video = null){
        if($video){
            $video = $video;
            // $preview_image = $video->preview_image;
        } else {
            $video = $this->model;
        } 
        
        if(array_key_exists('preview_image',$data)){
            if($video) ( new ImageUoloadService())->deleteImage($video->preview_image);
            
            $video->preview_image = ( new ImageUoloadService())->storeImage($data['preview_image'],'shop/video/',506,284);
        }
            
        $video->shop_id = $shop_id;
        $video->link = $data['link'];
        $video->status = $data['status'];
        $video->save();

        return $video;
    }
}