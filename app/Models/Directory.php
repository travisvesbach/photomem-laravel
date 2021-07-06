<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $fillable = [
        'path',
        'directory_id',
        'image_count',
        'total_image_count'
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

    // public function setTotalImageCount() {
    //     $count = $this->image_count;
    //     foreach($this->directories as $directory) {
    //         $count += $directory->image_count;
    //     }
    //     $this->total_image_count = $count;
    //     $this->save();
    // }
}
