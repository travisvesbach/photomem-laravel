@extends('layouts.app')

@section('title')
    Broken Photos
@endsection

@section('content')
    @if($photos->count() > 0)
        <div class="row">
            <h2><span id="broken-count">{{ $photos->count() }}</span> broken photos</h2>
            <button class="button" onclick="runSync('{{ route('sync.photos.broken') }}')">Resync Broken Photos</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Path</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($photos as $photo)
                    <tr id="{{ $photo->id }}" class="broken-row">
                        <td>{{ $photo->path() }}</td>
                        <td>
                            @if($photo->date_taken->year <= 1970)
                                unknown
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <h2>All of your photos have dates</h2>
    @endif
@endsection
