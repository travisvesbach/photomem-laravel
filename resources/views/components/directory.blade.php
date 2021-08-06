<div>
    <div class="directory-row {{ $directory->status == 'ignored' ? 'disabled' : '' }}" id="directory-{{ $directory->id }}">
        <p>
            <strong>Path:</strong> {{ $directory->displayPath() }} <br>
            <strong>Status:</strong> <span class="directory-status">{{ $directory->status }}</span> <br>
            <span class="ignored-hidden" style="display: {{ $directory->status == 'ignored' ? 'none' : 'block' }};">
                <strong>Count:</strong>
                <span class="directory-photo-count">{{ $directory->photo_count }}</span>
                @if($directory->directories->count() > 0)
                    [<span class="directory-total-photo-count" title="includes child directories">{{ $directory->total_photo_count }}</span>]
                @endif
            </span>
        </p>

        <button
            class="button button-sync"
            onclick="runSync('{{ route('sync.directory', ['directory' => $directory]) }}')"
            title="syncs photos in this directory and non-ignored child directories">
                {{ $directory->status == 'synced' || $directory->status == 'syncing' ? 'Resync' : 'Sync' }}
        </button>

        <button
            class="button button-danger ignored-hidden"
            onclick="ignoreDirectory('{{ route('directories.update', ['directory' => $directory]) }}' )"
            title="removes all photos from this directory and all child directories"
            style="display: {{ $directory->status == 'ignored' ? 'none' : 'block' }};">
                Ignore
        </button>
    </div>

    @if($directory->directories->count() > 0)
        <button class="accordion-control {{ $directory->depth() % 2 == 1 ? 'accordion-light' : '' }}">Child Directories</button>
        <div class="accordion-panel accordion {{ $directory->depth() % 2 == 1 ? 'accordion-light' : '' }}">
            @foreach($directory->directories as $child)
                <x-directory :directory="$child" />
                @if(!$loop->last)
                    <hr>
                @endif
            @endforeach
        </div>
    @endif
</div>
