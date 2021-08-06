@extends('layouts.app')

@section('title')
    Search
@endsection

@section('content')
    <h2>Search by Date</h2>

    <div>
        <form action="{{ route('photos.search') }}" method="GET">
            {{ csrf_field() }}

            <label for="month">Month:</label>
            <select id="month" name="month">
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $month)
                    <option {{ $month == $date->format('F') ? 'selected' : '' }}>{{ $month }}</option>
                @endforeach
            </select>

            <label for="day">Day:</label>
            <select id="day" name="day">
                @for ($day = 1; $day <= 31; $day++)
                    <option {{ $day == $date->format('d') ? 'selected' : '' }}>{{ $day }}</option>
                @endfor
            </select>

            <button class="button">Search</button>
        </form>
    </div>

    <div>
        @if($taken_on_date->count() > 0)
            <h2>{{ $taken_on_date->count() }} {{ $taken_on_date->count() != 1 ? 'Photos' : 'Photo' }} taken on {{ $date->format('F j') }}</h2>
            <table>
                <thead>
                    <tr>
                        <th>Path</th>
                        <th>Years</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(App\Models\Directory::hasTakenOnDate($date)->get() as $dir)
                        <tr>
                            <td>{{ $dir->displayPath() }}</td>
                            <td>{{ implode(', ', $dir->getYearsTakenOnDate($date)) }}</td>
                            <td>{{ $dir->getPhotosTakenOnDate($date)->count() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <h2>No photos taken on {{ $date->format('F j') }}</h2>
        @endif
    </div>
@endsection
