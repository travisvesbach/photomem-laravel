<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use File;
use Carbon\Carbon;

class PhotosController extends Controller
{
    public function random(Request $request) {
        $photo = Photo::todayOrRandom($request->today, $request->orientation);

        while(!File::exists($photo->path())) {
            $directory = $photo->directory;
            $photo->delete();
            $directory->setPhotoCounts();
            $photo = Photo::todayOrRandom($request->today, $request->orientation);
        }

        $path = $photo->escapedPath();

        $color = ' ';
        if(($request->color && $request->color == 'gray') || ($request->color && $request->color == 'grey')) {
            $color .= '-colorspace gray';
        }
        $orientation = ' -auto-orient ';

        $crop = ' ';
        if($request->crop) {
            $width = explode('x', $request->crop)[0];
            $height = explode('x', $request->crop)[1];

            $crop .= '-resize ' . $width . 'x' . $height . '^ -gravity center -extent ' . $width . 'x' . $height;
        }

        $date = ' ';
        if($request->date && $photo->date_taken) {
            $date .= "-fill white -pointsize 48 -undercolor '#0008' -gravity Southeast -draw 'text 0,-9 \"" .  $photo->date_taken->format('n/j/Y') . "\"' ";
        }


        $tmp = explode('.', $photo->name);
        $extension = end($tmp);
        if($request->format && $request->format != 'bytes') {
            $extension = $request->extension;
        }

        $destination = public_path('storage') . '/converted.' . $extension;

        $command = 'convert ' . $path . $color . $orientation . $crop . $date . $destination;

        shell_exec($command);

        if($request->format && $request->format == 'bytes') {
            $script = public_path('scripts') . '/imgconvert.py';
            $new_destination = public_path('storage') . '/converted.txt';
            shell_exec("python3 " . $script . " -i " . $destination . " -n pic -o " . $new_destination . ' 2>&1');
            return response()->file($new_destination);
        }
        return response()->file($destination);
    }

    public function search(Request $request) {
        if($request->month && $request->day) {
            $date = Carbon::createFromFormat('F j', $request->month . ' ' . $request->day);
            $taken_on_date = \App\Models\Photo::takenOnDate($date)->get();
        } else {
            $date = Carbon::now();
            $taken_on_date = \App\Models\Photo::takenOnDate()->get();
        }

        return view('photos.search', [
            'date' => $date,
            'taken_on_date' => $taken_on_date
        ]);
    }

    public function broken() {
        $photos = Photo::broken()->get();

        return view('photos.broken', [
            'photos' => $photos
        ]);
    }
}
