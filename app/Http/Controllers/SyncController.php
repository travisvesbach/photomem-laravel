<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Directory;
use App\Models\Photo;
use Queue;
use DB;
use Illuminate\Queue\Jobs\Job;
use App\Jobs\SyncBrokenPhotos;

class SyncController extends Controller
{
    public function syncDirectory(Directory $directory) {
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
        $directory->addSyncPhotosJob();

        return $this->syncStatus();
    }

    public function syncDirectories() {
        Directory::deleteNonexistant();
        Directory::syncSubDirectories(public_path('storage').'/sync/*');
        return json_encode(['directories' => Directory::orderBy('path')->get()]);
    }

    public function syncBrokenPhotos() {
        SyncBrokenPhotos::dispatch();
        return $this->syncStatus();
    }

    public function syncStatus() {
        $status = 'done';
        $current = null;
        $response = null;
        if(Queue::size() > 0) {
            $status = 'syncing';
            if(json_decode(DB::table('jobs')->first()->payload)->displayName == 'App\\Jobs\\SyncBrokenPhotos') {
                $current = 'Broken Photos';
                $response = [
                    'status' => $status,
                    'current' => $current,
                    'broken_photo_ids' => Photo::broken()->pluck('id'),
                ];
            } else {
                $current = str_replace(public_path('storage') . '/sync/', '', unserialize(json_decode(DB::table('jobs')->first()->payload)->data->command)->directory->path);
                $response = [
                    'status' => $status,
                    'directories' => Directory::orderBy('path')->get(),
                    'current' => $current,
                    'photo_count' => Photo::all()->count(),
                ];
            }
        } else {
            $response = [
                'status' => $status,
                'directories' => Directory::orderBy('path')->get(),
                'current' => $current,
                'photo_count' => Photo::all()->count(),
                'broken_photo_ids' => Photo::broken()->pluck('id'),
            ];
        }
        return json_encode($response);
    }
}
