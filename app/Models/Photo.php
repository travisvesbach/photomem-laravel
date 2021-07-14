<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Directory;

class Photo extends Model
{
    protected $dates = ['created_at', 'updated_at', 'date_taken'];

    public function directory() {
        return $this->belongsTo(Directory::class);
    }

    public function path() {
        return $this->directory->path . '/' . $this->name;
    }

    public function escapedPath() {
        return str_replace(
            [' ', '\'', '&'],
            ['\ ', '\\\'', '\&'],
            $this->directory->path . '/' . $this->name
        );
        // return self::escapePath($this->directory->path . '/' . $this->name);
    }

    public function scopeTakenToday($query) {
        return $query->whereRaw("strftime( '%m', photos.date_taken ) = strftime('%m','now', 'localtime') AND strftime( '%d', photos.date_taken ) = strftime('%d','now', 'localtime')");
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
    public static function todayOrRandom($today = false, $orientation = null) {
        if($today) {
            $photos = self::takenToday()->orientation($orientation)->get();
            if($photos->count() > 0) {
                return $photos->random();
            }
        }
        return self::orientation($orientation)->random();
    }

    public static function escapePath($path) {
        return str_replace(
            [' ', '\'', '&'],
            ['\ ', '\\\'', '\&'],
            $path
        );
    }
}
