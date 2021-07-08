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

    public function scopeOrientation($query, $orientation = null) {
        if($orientation) {
            return $query->where('orientation', $orientation);
        }
        return $query;
    }

    public static function scopeRandom($query) {
        return $query->get()->random();
    }

    // if none found for today with orientation, return random with orientation
    public static function todayOrRandom($orientation = null) {
        $pictures = self::takenToday()->orientation($orientation)->get();
        if($pictures->count() > 0) {
            return $pictures->random();
        }
        return self::orientation($orientation)->random();
    }
}
