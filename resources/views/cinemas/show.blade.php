@extends('layouts.default')
@section('title', $cinema->location . ' - What\'s Good at the Movies - MoviesOwl')
@section('content')

    <h1>{{ $cinema->location }} (Event)</h1>

    @foreach ($moviesByRating as $rating => $movies)
        <h2>{{ $rating }} Movies</h2>
    @foreach (array_chunk($movies, 4) as $movieRow)
        <div class="row">
            @foreach ($movieRow as $movie)
                <div class="col-xs-12 col-sm-3">
                    <div class="thumbnail">
                        <a href="{{ URL::to('cinemas/' . $cinema->slug . '/movies/' . $movie->slug . '/showings') }}">
                            <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}">
                        </a>
                        <div class="caption">
                            <h4>{{ $movie->title }}</h4>
                            <p>{{ $movie->tomato_meter }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
    @endforeach
@stop
