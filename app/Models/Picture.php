<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Directory;

class Picture extends Model
{
    public function directory() {
        return $this->belongsTo(Directory::class);
    }

    public function path() {
        return str_replace(
            [' ', '\''],
            ['\ ', '\\\''],
            $this->directory->path . '/' . $this->name
        );
    }
}
