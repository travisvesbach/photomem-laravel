<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Directory;
use Schema;

class PageController extends Controller
{
    public function index() {
        if(Schema::hasTable('photos')) {
            return view('index', [
                'migrated' => true,
                'photos' => Photo::all(),
                'photos_today' => Photo::takenOnDate()->get(),
                'directories' => Directory::orderBy('path')->get(),
                'directories_today' => Directory::hasTakenOnDate()->get(),
            ]);
        }
        return view('index', [
            'migrated' => false,
        ]);
    }

    public function about() {
        return view('about');
    }
}
