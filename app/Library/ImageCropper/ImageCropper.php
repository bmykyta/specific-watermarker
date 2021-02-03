<?php


namespace App\Library\ImageCropper;


class ImageCropper
{
    public static function cropWhiteBg()
    {
        $original_img = imagecreatefromjpeg('watermark-red.jpg');
        $without = imagecropauto($original_img, IMG_CROP_WHITE);
        return imagejpeg($without, 'rez.jpeg', 50 );
    }
}
