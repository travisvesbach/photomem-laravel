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
        return $this->directory->path . '/' . $this->name;
    }

    public function scopeTakenToday($query) {
        return $query->whereRaw("strftime( '%m', pictures.date_taken ) = strftime('%m','now', 'localtime') AND strftime( '%d', pictures.date_taken ) = strftime('%d','now', 'localtime')");
    }
}
