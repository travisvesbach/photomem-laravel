@extends('layouts.app')

@section('title')
    PhotoMem
@endsection

@section('content')

    @if(\Schema::hasTable('pictures'))
        <?php
            $pictures = \App\Models\Picture::all();
            $pictures_today = \App\Models\Picture::takenToday()->get();
            $directories = \App\Models\Directory::orderBy('path')->get();
        ?>
        @if($pictures->count() > 0)
            <div class="home-status">
                <div class="status-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    {{ $directories->count() }} total {{ $directories->count() != 1 ? 'directories' : 'directory' }}
                </div>
                <div class="status-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    {{ $directories->where('status', 'synced')->count() }} {{ $directories->where('status', 'synced')->count() != 1 ? 'directories' : 'directory' }} synced
                </div>
                <div class="status-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    {{ $directories->where('status', 'ignored')->count() }} {{ $directories->where('status', 'ignored')->count() != 1 ? 'directories' : 'directory' }} ignored
                </div>
                <div class="status-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $pictures->count() }} {{ $pictures->count() != 1 ? 'pictures' : 'picture' }} synced
                </div>
                <div class="status-item">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $pictures_today->count() }} {{ $pictures_today->count() != 1 ? 'pictures' : 'picture' }} taken today in history
                </div>
            </div>

            <div class="row">
                <a href="{{ route('pictures.random') }}?today=true&date=true" class="button">Today or Random</a>
                <a href="{{ route('pictures.random') }}?date=true" class="button">Random</a>
                <a href="{{ route('pictures.random') }}?orientation=landscape&date=true" class="button">Random Landscape</a>
                <a href="{{ route('pictures.random') }}?orientation=portrait&date=true" class="button">Random Portrait</a>
                {{-- <a href="<%= Web.routes.path(:subreddit) %>?sub=art&time=day" class="button">Daily Art Subreddit</a> --}}
                {{-- <a href="<%= Web.routes.path(:subreddit) %>?sub=imaginarysliceoflife&time=day" class="button">Daily ImaginarySliceOfLife Subreddit</a> --}}
            </div>

            @if($pictures_today->count() > 0)
                <h3>Images Taken Today</h3>

                <table>
                    <thead>
                        <tr>
                            <th>Path</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(App\Models\Directory::hasTakenToday()->get() as $dir)
                            <tr>
                                <td>{{ $dir->displayPath() }}</td>
                                <td>{{ $dir->getPicturesTakenToday()->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @else
            <p class="placeholder">There are no images synced yet. Visit <a href="{{ route('directories') }}">Directories</a> to sync directories.</p>
        @endif
    @else
        <h2 style="color: red;">Migrations have not been run.</h2>
    @endif


@endsection
