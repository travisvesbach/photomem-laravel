<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Picture;

class PicturesController extends Controller
{
    public function random(Request $request) {
        if($request->today) {
            $picture = Picture::todayOrRandom($request->orientation);
        } else {
            $picture = Picture::orientation($request->orientation)->random();
        }

        $path = $picture->escapedPath();

        $color = ' ';
        if(($request->color && $request->color == 'gray') || ($request->color && $request->color == 'grey')) {
            $color .= '-colorspace gray';
        }

        $crop = ' ';
        if($request->crop) {
            $width = explode('x', $request->crop)[0];
            $height = explode('x', $request->crop)[1];

            $crop .= '-resize ' . $width . 'x' . $height . '^ -gravity center -extent ' . $width . 'x' . $height;
        }

        $date = ' ';
        if($request->date && $picture->date_taken) {
            $date .= "-fill white -pointsize 48 -undercolor '#0008' -gravity Southeast -draw 'text 0,-9 \"" .  $picture->date_taken->format('n/j/Y') . "\"' ";
        }


        $tmp = explode('.', $picture->name);
        $extension = end($tmp);
        if($request->extension && $request->extension != 'bytes') {
            $extension = $request->extension;
        }

        $destination = public_path('storage') . '/converted.' . $extension;

        $command = 'convert ' . $path . $color . $crop . $date . $destination;

        shell_exec($command);

        if($request->format && $request->format == 'bytes') {
            $script = public_path('scripts') . '/imgconvert.py';
            $new_destination = public_path('storage') . '/converted.txt';
            shell_exec("python3 " . $script . " -i " . $destination . " -n pic -o " . $new_destination . ' 2>&1');
            return response()->file($new_destination);
        }
        return response()->file($destination);
    }
}
