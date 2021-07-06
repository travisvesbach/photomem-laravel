<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Directory;
// use \RecursiveIteratorIterator;
// use \RecursiveDirectoryIterator;
use File;

class Directories extends Component
{
    public $directories;
    public $synced;
    public $ignored;
    public $alert;

    public function render()
    {
        $this->directories = Directory::all();
        $this->synced = $this->directories->where('status', 'synced')->count();
        $this->ignored = $this->directories->where('status', 'ignored')->count();


        return view('livewire.directories');
    }

    public function syncDirectories() {
        $this->syncSubDirectories('./storage/sync/*');
        $this->alert = 'Syncing Directories';

        foreach(Directory::all() as $directory) {
            if(!File::exists($directory->path)) {
                $directory->delete();
            }
        }
        $this->alert = null;
        $this->emit('directoriesSynced');
    }

    protected function syncSubDirectories($dir_path, $parent_id = null) {
        $directories = array_filter(glob($dir_path), 'is_dir');
        foreach($directories as $directory) {
            $directory_obj = Directory::firstOrCreate(
                ['path' => $directory],
                ['directory_id' => $parent_id]
            );

            $this->syncSubDirectories($directory.'/*', $directory_obj->id);
        }
    }
}
