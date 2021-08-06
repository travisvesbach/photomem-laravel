<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Photo;

class SyncBrokenPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $photos = Photo::broken()->get();
        foreach($photos as $photo) {
            var_dump($photo->name);

            $photo->date_taken = date('Y-m-d H:i:s', strtotime(shell_exec("identify -format '%[EXIF:DateTimeOriginal]' " . $photo->escapedPath())));
            $photo->save();
        }
    }
}
