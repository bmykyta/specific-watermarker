<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\ImageSampler\ImageSampler;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class HomeController extends Controller
{
    public function index()
    {
        return view('upload_file');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpeg,png,jpg',
        ]);
        $extension = $request->file->extension();
        $filename = sha1(time()).".".$extension;
        $request->file->move(public_path('uploads'), $filename);
        $file_path = "uploads/{$filename}";
        $watermarkedImg = $this->watermarker($file_path);
        $encodedImg = base64_encode(File::get($watermarkedImg));
        File::delete($file_path);
        File::delete($watermarkedImg);

        return redirect('file')->with('success', 'Image successfully uploaded!')->with('encode_img', $encodedImg);
    }

    public function downloadWatermark(Request $request)
    {
        $image = $request->file_download;
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = sha1(time()).'.png';
        $filename = "uploads/{$imageName}";
        File::put($filename, base64_decode($image));

        return response()->download($filename, 'watermarked.png')->deleteFileAfterSend();
    }

    private function watermarker($filename)
    {
        $sampler = new ImageSampler(public_path($filename));
        $sampler->set_percent(50);
        $sampler->set_steps(1);
        $matrix = $sampler->sample();
        $color = $this->checkColor($matrix[0][0]);
        $img = Image::make(public_path($filename));
        $watermark = $this->getWatermarkByColor($color);
        $watermark = $watermark->trim('bottom-right', null, 89)->opacity(60)->rotate(35);
        $newFilename = 'uploads/'.sha1(time()).".png";
        $img->insert($watermark, 'center', 0, 60)->save(public_path($newFilename));

        return $newFilename;
    }

    private function checkColor($array): string
    {
        $maxIndex = array_search(max($array), $array);
        $color = '';
        if ($maxIndex === 0) {
            $color = 'red';
        } else if ($maxIndex === 1) {
            $color = 'green';
        } else {
            $color = 'blue';
        }

        return $color;
    }

    /**
     * Should be in Model
     *
     * @param $color
     */
    private function getWatermarkByColor(string $color)
    {
        $watermark = '';
        if ($color === 'red') {
            $watermark = Image::make(public_path('images/watermark-black-removebg.png'));
        } else if ($color === 'green') {
            $watermark = Image::make(public_path('images/watermark-red-removebg.png'));
        } else if ($color === 'blue') {
            $watermark = Image::make(public_path('images/watermark-yellow-removebg.png'));
        }

        return $watermark;
    }
}
