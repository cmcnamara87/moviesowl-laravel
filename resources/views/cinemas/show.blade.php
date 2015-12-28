@extends('layouts.default')
@section('title', $cinema->location . ' - What\'s Good at the Movies - MoviesOwl')
@section('content')

    <div class="jumbotron">
        <h1 style="text-align: left;">{{ $cinema->location }}</h1>
        <p>Cinema</p>
    </div>

    @foreach ($moviesByRating as $rating => $movies)
        <h2>{{ $rating }} Movies</h2>
    @foreach (array_chunk($movies, 3) as $movieRow)
        <div class="row">
            @foreach ($movieRow as $movie)
                <div class="col-xs-12 col-sm-4">
                    <div class="thumbnail">
                        <a href="{{ URL::to('cinemas/' . $cinema->slug . '/movies/' . $movie->slug . '/showings') }}">
                            <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}">
                        </a>
                        <div class="caption">
                            <h4>{{ $movie->title }}</h4>
                            <p>
                                @if ($movie->tomato_meter > 75)
                                    <img src="/images/CF_240x240.png" alt="" class="tomato-rating"/>
                                @elseif ($movie->tomato_meter > 59)
                                    <img src="/images/fresh.png" alt="" class="tomato-rating"/>
                                @else
                                    <img src="/images/rotten.png" alt="" class="tomato-rating" />
                                @endif
                                {{ $movie->tomato_meter }}%
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
    @endforeach
@stop
