<?php

namespace App\Infrastructure;

use Illuminate\Support\Facades\Storage;

class AppConstant {

	public const encrypt_kk = 'erl';
    public static $secret_iv = "kroonal123";
    public static $secret_key = "kroonal";
    public static $app_media_folder_name = "media";
    public static $orderId = "order-id";
    public static $defaultImageName = "kk-693378000.png";
    
    public static function storeImage($image){
        $storeFile = $image->store(self::$app_media_folder_name);
        $fileName = str_replace(self::$app_media_folder_name."/", "", $storeFile);
        return $fileName;
    }

    public static function getImage($imageName){
        return asset('storage/app/'.self::$app_media_folder_name.'/'.$imageName);
    }

    public static function getImageThumb($imageName){
        return asset('storage/app/'.self::$app_media_folder_name.'/thumb/'.$imageName);
    }

    public static function deleteImage($imageName){
        Storage::delete(self::$app_media_folder_name.'/'.$imageName);
    }

    public static function deleteThumbImage($imageName){
        Storage::delete(self::$app_media_folder_name.'/thumb/'.$imageName);
    }
}
