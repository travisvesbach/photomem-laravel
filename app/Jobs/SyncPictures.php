<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Directory;
use App\Models\Picture;

class SyncPictures implements ShouldQueue
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
        $this->directory->syncPictures();

        // set parent picture counts
        foreach($this->directory->getParentDirectories() as $parent) {
            $parent->setPictureCounts();
        }
    }
}
