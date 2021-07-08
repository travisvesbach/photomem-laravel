<div>
    <div class="directory-row {{ $directory->status == 'ignored' ? 'disabled' : '' }}" id="directory-{{ $directory->id }}">
        <p>
            <strong>Path:</strong> {{ $directory->displayPath() }} <br>
            <strong>Status:</strong> <span class="directory-status">{{ $directory->status }}</span> <br>
            <span class="ignored-hidden" style="display: {{ $directory->status == 'ignored' ? 'none' : 'block' }};">
                <strong>Count:</strong>
                <span class="directory-picture-count">{{ $directory->picture_count }}</span>
                @if($directory->directories->count() > 0)
                    [<span class="directory-total-picture-count" title="includes child directories">{{ $directory->total_picture_count }}</span>]
                @endif
            </span>
        </p>

        <button
            class="button button-sync"
            onclick="runSync('{{ route('directories.sync', ['directory' => $directory]) }}')"
            title="syncs pictures in this directory and non-ignored child directories">
                {{ $directory->status == 'synced' ? 'Resync' : 'Sync' }}
        </button>

        <button
            class="button button-danger ignored-hidden"
            onclick="ignoreDirectory('{{ route('directories.update', ['directory' => $directory]) }}' )"
            title="removes all pictures from this directory and all child directories"
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
