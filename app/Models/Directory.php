<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;
use File;
use Image;
use App\Jobs\SyncPictures;

class Directory extends Model
{
    protected $fillable = [
        'path',
        'directory_id',
        'picture_count',
        'total_picture_count'
    ];

    public function directory() {
        return $this->belongsTo(Directory::class);
    }

    public function directories() {
        return $this->hasMany(Directory::class);
    }

    public function getParentDirectories() {
        $directory = $this;
        $parents = [];
        while(!is_null($directory->directory_id)) {
            $directory = $directory->directory;
            $parents[] = $directory;
        }

        return $parents;
    }

    public function depth() {
        return count($this->getParentDirectories());
    }

    public function displayPath() {
        return explode(env('SYNC_DIRECTORY'), $this->path)[1];
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function setPictureCounts() {
        $this->picture_count = $this->pictures->count();
        $count = $this->picture_count;
        foreach($this->directories as $directory) {
            $count += $directory->total_picture_count;
        }
        $this->total_picture_count = $count;
        $this->save();
    }

    public function deletePictures() {
        $this->pictures()->delete();
    }

    public function deleteNonexistantPictures() {
        foreach($this->pictures as $picture) {
            if(!File::exists($picture->path())) {
                $picture->delete();
            }
        }
    }

    public function absolutePath() {
        return env('PUBLIC_DIRECTORY') . substr($this->path, 1);
    }

    // syncs pictures in self and non-ignored subdirectories
    public function syncPictures() {
        $this->status = 'unsynced';
        $this->save();
        $this->deleteNonexistantPictures();

        $picture_array = [];
        foreach (glob($this->path . "/*.{jpg,png,jpeg,JPG,PNG,JPEG}", GLOB_BRACE) as $filename) {
            $arr = explode('/', $filename);
            $name = end($arr);

            // if directory doesn't contain picture with name
            if(!$this->pictures->where('name', $name)->count() > 0) {
                if($image = Image::make($filename)) {

                    $orientation = null;
                    if($image->exif('Orientation') == 1 || $image->exif('Orientation') == 3) {
                        $orientation = 'landscape';
                    } elseif($image->exif('Orientation') == 6 || $image->exif('Orientation') == 8) {
                        $orientation = 'portrait';
                    } elseif($image->width() > $image->height()) {
                        $orientation = 'landscape';
                    } elseif($image->width() < $image->height()) {
                        $orientation = 'portrait';
                    }

                    $picture = [
                        'name' => $name,
                        'date_taken' => $image->exif('DateTimeOriginal') ? date('Y-m-d H:i:s', strtotime($image->exif('DateTimeOriginal'))) : null,
                        'directory_id' => $this->id,
                        'orientation' => $orientation
                    ];

                    $picture_array[] = $picture;

                    if(count($picture_array) >= 100) {
                        Picture::insert($picture_array);
                        $picture_array = [];
                    }
                }
            }
        }

        if(count($picture_array) > 0) {
            Picture::insert($picture_array);
        }

        $this->refresh();
        $this->status = 'synced';
        $this->save();
        $this->setPictureCounts();
    }

    public function addSyncPicturesJob() {
        if($this->status != 'ignored') {
            SyncPictures::dispatch($this);

            foreach($this->directories as $directory) {
                $directory->addSyncPicturesJob();
            }
        }
    }

    // set self and child directories' status to 'ignored'
    public function ignore() {
        $this->deletePictures();
        $this->status = 'ignored';
        $this->picture_count = 0;
        $this->total_picture_count = 0;
        $this->save();

        foreach ($this->directories as $child) {
            $child->ignore();
        }
    }

    public static function syncSubDirectories($dir_path, $parent_id = null) {
        $directories = array_filter(glob($dir_path), 'is_dir');
        foreach($directories as $directory) {
            $directory_obj = self::firstOrCreate(
                ['path' => $directory],
                ['directory_id' => $parent_id]
            );

            self::syncSubDirectories($directory.'/*', $directory_obj->id);
        }
    }

    // remove nonexistent directories
    public static function deleteNonexistant() {
        foreach(self::all() as $directory) {
            if(!File::exists($directory->path)) {
                $directory->delete();
            }
        }
    }
}
