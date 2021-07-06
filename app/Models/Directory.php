<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Picture;
use File;
use Image;

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
        return explode('./storage/sync/', $this->path)[1];
    }

    public function pictures() {
        return $this->hasMany(Picture::class);
    }

    public function setTotalPictureCount() {
        $count = $this->picture_count;
        foreach($this->directories as $directory) {
            $count += $directory->picture_count;
        }
        $this->total_picture_count = $count;
        $this->save();
    }

    public function deletePictures() {
        $this->pictures->delete();
    }

    public function deleteNonexistantPictures() {
        foreach($this->pictures as $picture) {
            if(!File::exists($this->path)) {
                $picture->delete();
            }
        }
    }

    public function syncPictures() {
        $this->status = 'unsynced';
        $this->save();
        $this->deleteNonexistantPictures();

        $picture_array = [];
        foreach (glob($this->path."/*.{jpg,png,jpeg,JPG,PNG,JPEG}", GLOB_BRACE) as $filename) {
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
        $this->picture_count = $this->pictures->count();
        $this->save();
    }
}
