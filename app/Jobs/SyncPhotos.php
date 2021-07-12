<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Directory;
use App\Models\Photo;

class SyncPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $directory;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Directory $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        var_dump($this->directory->displayPath());
        var_dump('starting photo_count: ' . $this->directory->photo_count);
        $this->directory->syncPhotos();

        // set parent photo counts
        foreach($this->directory->getParentDirectories() as $parent) {
            $parent->setPhotoCounts();
        }
        var_dump('ending photo_count: ' . $this->directory->photo_count);
    }
}
