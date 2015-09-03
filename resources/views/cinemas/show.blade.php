@extends('layouts.default')
@section('title', $cinema->location . ' - What\'s Good at the Movies - MoviesOwl')
@section('content')

    <h1>What's good at {{ $cinema->location }} (Event)</h1>

    @foreach (array_chunk($movies->all(), 4) as $movieRow)
        <div class="row">
            @foreach ($movieRow as $movie)
                <div class="col-xs-12 col-sm-3">
                    <div class="thumbnail">
                        <a href="{{ URL::to('cinemas/' . $cinema->slug . '/movies/' . $movie->slug . '/showings') }}">
                            <img src="{{ $movie->poster }}" alt="{{ $movie->title  }}">
                        </a>
                        <div class="caption">
                            <p>{{ $movie->tomato_meter }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@stop
