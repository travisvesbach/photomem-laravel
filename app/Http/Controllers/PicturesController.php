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

        return response()->file($picture->path());
    }
}
