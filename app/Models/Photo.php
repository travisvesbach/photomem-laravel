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
        return self::escapePath($this->directory->path . '/' . $this->name);
    }

    public function scopeTakenOnDate($query, $date = 'now') {
        // DATE_FORMAT for and strftime for sqlite
        if(env('DB_CONNECTION') == 'mysql') {
            $date = $date == 'now' ? 'NOW()' : "'".$date."'";
            return $query->whereRaw("DATE_FORMAT(photos.date_taken, '%m-%d') = DATE_FORMAT(". $date .", '%m-%d')");
        } elseif(env('DB_CONNECTION') == 'sqlite') {
            return $query->whereRaw("strftime( '%m', photos.date_taken ) = strftime('%m','". $date ."', 'localtime') AND strftime( '%d', photos.date_taken ) = strftime('%d','". $date ."', 'localtime')");
        }
    }

    public function scopeOrientation($query, $orientation = null) {
        $base_query = clone $query;
        $query->where('orientation', $orientation);
        if($orientation && $query->count() > 0) {
            return $query;
        }
        return $base_query;
    }

    public static function scopeRandom($query) {
        return $query->get()->random();
    }

    public function scopeBroken($query) {
        return $query->whereYear('date_taken', '<', 1990);
    }

    // if none found for today with orientation, return random with orientation
    public static function todayOrRandom($today = false, $orientation = null) {
        if($today) {
            $photos = self::takenOnDate()->orientation($orientation)->get();
            if($photos->count() > 0) {
                return $photos->random();
            }
        }
        return self::orientation($orientation)->random();
    }

    public static function escapePath($path) {
        return str_replace(
            [' ', '\'', '&', '(', ')'],
            ['\ ', '\\\'', '\&', '\(', '\)'],
            $path
        );
    }

}
