<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;

class ImageUoloadService
{
    // protected $url = '/home/twowheel/public_html/admin/images/';
    protected $url = 'images/';

    function storeImage($image, $path, $width, $height)
    {
        $image_name = hexdec(uniqid());
        $ext = strtolower($image->getClientOriginalExtension());
        $image_full_name = $image_name . '.' . $ext;
        $upload_path = $path;
        $upload_path1 = Image_BASE_URL() . $path;
        $image_url = $upload_path . $image_full_name;
        // $success         = $image->move($upload_path1, $image_full_name);
        $img = Image::make($image)->resize($width, $height);
        $img->save($upload_path1 . $image_full_name, 75);

        return $image_url;
    }

    // delete image
    function deleteImage($image)
    {
        File::delete($image);
    }
    function deleteFile($file)
    {
        File::delete($file);
    }

    // store pdf
    function storeFile($file, $path)
    {
        $pdf_name = hexdec(uniqid());
        $ext = strtolower($file->getClientOriginalExtension());
        $pdf_full_name = $pdf_name . '.' . $ext;
        $upload_path = $path;
        $upload_path1 =  $this->url . $path;
        $file_url = $upload_path . $pdf_full_name;
        $success = $file->move($upload_path1, $pdf_full_name);

        return $file_url;
    }
}
