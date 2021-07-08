@extends('layouts.app')

@section('title')
    Directories
@endsection

@section('content')
    <div class="alert alert-syncing">
        <div class="lds-dual-ring"></div>
        <div class="alert-message">Syncing...please wait</div>
    </div>

    <div class="row">
        <div class="synced-status">
            <div class="status-item" title="total directories">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <span id="directories-count">{{ $directories->count() }}</span>
            </div>
            <div class="status-item" title="directories synced">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <span id="directories-synced-count">{{ $synced }}</span>
            </div>
            <div class="status-item" title="directories ignored">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                </svg>
                <span id="directories-ignored-count">{{ $ignored }}</span>
            </div>
            <div class="status-item" title="pictures synched">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span id="picture-count">{{ $picture_count }}</span>
            </div>
        </div>

        <div class="actions">
            <button class="button" title="syncs all directories (no pictures)" onclick="runSync('{{ route('directories.syncDirectories') }}')">Sync Directories</button>
        </div>
    </div>

    <div class="directories accordion">
        @foreach($directories->where('directory_id', null) as $directory)
            <x-directory :directory="$directory"/>
            @if(!$loop->last)
                <hr>
            @endif
        @endforeach
    </div>

@endsection
