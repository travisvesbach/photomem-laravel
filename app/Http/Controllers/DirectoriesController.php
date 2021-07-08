<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Directory;
use App\Models\Picture;
use File;
use Queue;
use Illuminate\Queue\Jobs\Job;
use DB;

class DirectoriesController extends Controller
{
    public function index() {
        $directories = Directory::orderBy('path')->get();

        return view('directories.index', [
            'directories' => $directories,
            'synced' => $directories->where('status', 'synced')->count(),
            'ignored' => $directories->where('status', 'ignored')->count(),
            'picture_count' => Picture::all()->count(),
        ]);
    }

    public function update(Directory $directory, Request $request) {
        if($request->input('status') == 'true') {
            $directory->ignore();
            foreach($directory->getParentDirectories() as $parent) {
                $parent->setPictureCounts();
            }
        }

        return json_encode([
            'directories' => Directory::orderBy('path')->get(),
            'picture_count' => Picture::all()->count(),
        ]);
    }

    public function sync(Directory $directory) {
        // when syncing an ignored directory, ignored parents marked as unsynced
        if($directory->status == 'ignored') {
            $directory->status = 'unsynced';
            foreach($directory->getParentDirectories() as $parent) {
                if($parent->status == 'ignored') {
                    $parent->status = 'unsynced';
                    $parent->save();
                }
            }
        }

        Directory::deleteNonexistant();

        // create child directories that don't exist
        Directory::syncSubDirectories($directory->path, $directory->id);

        // create SyncPitcures jobs for current directory and all child directories
        $directory->addSyncPicturesJob();

        return $this->syncStatus();
    }

    public function syncDirectories() {
        Directory::deleteNonexistant();
        Directory::syncSubDirectories(env('SYNC_DIRECTORY').'*');
        return json_encode(['directories' => Directory::orderBy('path')->get()]);
    }

    public function syncStatus() {
        $status = 'done';
        $current = null;
        if(Queue::size() > 0) {
            $status = 'syncing';
            $current = str_replace(env('SYNC_DIRECTORY'), '', unserialize(json_decode(DB::table('jobs')->first()->payload)->data->command)->directory->path);
        }
        return json_encode([
            'status' => $status,
            'directories' => Directory::orderBy('path')->get(),
            'current' => $current,
            'picture_count' => Picture::all()->count(),
        ]);
    }

}
