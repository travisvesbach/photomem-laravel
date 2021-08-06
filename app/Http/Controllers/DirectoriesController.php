<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Directory;
use App\Models\Photo;

class DirectoriesController extends Controller
{
    public function index() {
        $directories = Directory::orderBy('path')->get();

        return view('directories.index', [
            'directories' => $directories,
            'synced' => $directories->where('status', 'synced')->count(),
            'ignored' => $directories->where('status', 'ignored')->count(),
            'photo_count' => Photo::all()->count(),
        ]);
    }

    public function update(Directory $directory, Request $request) {
        if($request->input('status') == 'true') {
            $directory->ignore();
            foreach($directory->getParentDirectories() as $parent) {
                $parent->setPhotoCounts();
            }
        }

        return json_encode([
            'directories' => Directory::orderBy('path')->get(),
            'photo_count' => Photo::all()->count(),
        ]);
    }
}
