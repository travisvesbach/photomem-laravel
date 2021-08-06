<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Photo;
use File;
use Image;
use App\Jobs\SyncPhotos;

class Directory extends Model
{
    protected $fillable = [
        'path',
        'directory_id',
        'photo_count',
        'total_photo_count'
    ];

    protected $dates = ['created_at', 'updated_at'];

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
        return explode(public_path('storage') . '/sync/', $this->path)[1];
    }

    public function photos() {
        return $this->hasMany(Photo::class);
    }

    public function getPhotosTakenOnDate($date = 'now') {
        return $this->photos()->takenOnDate($date)->get();
    }

    public function getYearsTakenOnDate($date = 'now') {
        $years = [];
        foreach($this->photos()->takenOnDate($date)->pluck('date_taken') as $date) {
            array_push($years, $date->format('Y'));
        }
        $years = array_unique($years);
        sort($years);
        return $years;
    }

    public function setPhotoCounts() {
        $this->photo_count = $this->photos->count();
        $count = $this->photo_count;
        foreach($this->directories as $directory) {
            $count += $directory->total_photo_count;
        }
        $this->total_photo_count = $count;
        $this->save();
        $this->setParentPhotoCounts();
    }

    public function setParentPhotoCounts() {
        foreach($this->getParentDirectories() as $parent) {
            $parent->setPhotoCounts();
        }
    }

    public function deletePhotos() {
        $this->photos()->delete();
    }

    public function deleteNonexistantPhotos() {
        foreach($this->photos as $photo) {
            if(!File::exists($photo->path())) {
                $photo->delete();
            }
        }
    }

    // syncs photos in self and non-ignored subdirectories
    public function syncPhotos() {
        $this->status = 'syncing';
        $this->save();
        $this->deleteNonexistantPhotos();

        $photo_array = [];
        foreach (glob($this->path . "/*.{jpg,png,jpeg,JPG,PNG,JPEG}", GLOB_BRACE) as $filename) {
            $arr = explode('/', $filename);
            $name = end($arr);

            // if directory doesn't contain photo with name
            if(!$this->photos->where('name', $name)->count() > 0) {

                $escaped_path = Photo::escapePath($filename);

                $orientation = shell_exec("identify -format '%[EXIF:Orientation]' " . $escaped_path);
                if($orientation == 1 || $orientation == 3) {
                    $orientation = 'landscape';
                } elseif($orientation == 6 || $orientation == 8) {
                    $orientation = 'portrait';
                }

                // save two imagemagick commands if orientation is already set from exif data
                if($orientation != 'landscape' && $orientation != 'portrait') {
                    $width = shell_exec("identify -format '%[width]' " . $escaped_path);
                    $height = shell_exec("identify -format '%[height]' " . $escaped_path);
                    if($width > $height) {
                        $orientation = 'landscape';
                    } elseif($width < $height) {
                        $orientation = 'portrait';
                    }
                }


                $photo = [
                    'name' => $name,
                    'date_taken' => date('Y-m-d H:i:s', strtotime(shell_exec("identify -format '%[EXIF:DateTimeOriginal]' " . $escaped_path))),
                    'directory_id' => $this->id,
                    'orientation' => $orientation
                ];

                $photo_array[] = $photo;
                if(count($photo_array) >= 100) {
                    Photo::insert($photo_array);
                    $photo_array = [];
                }
            }
        }

        if(count($photo_array) > 0) {
            Photo::insert($photo_array);
        }

        $this->refresh();
        $this->status = 'synced';
        $this->save();
        $this->setPhotoCounts();
    }

    public function addSyncPhotosJob() {
        if($this->status != 'ignored') {
            SyncPhotos::dispatch($this);

            foreach($this->directories as $directory) {
                $directory->addSyncPhotosJob();
            }
        }
    }

    // set self and child directories' status to 'ignored'
    public function ignore() {
        $this->deletePhotos();
        $this->status = 'ignored';
        $this->photo_count = 0;
        $this->total_photo_count = 0;
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
                $directory->deletePhotos();
                $directory->delete();
            }
        }
    }

    // get all directories that have photos taken on the passed date in history (today if date is null)
    public function scopeHasTakenOnDate($query, $date = 'now') {
        return $query->whereHas('photos', function($query) use($date) {
            $query->takenOnDate($date);
        });
    }
}
