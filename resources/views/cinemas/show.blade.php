@extends('layouts.default')
@section('title', $cinema->location . ' - What\'s Good at the Movies - MoviesOwl')
@section('content')

    <div class="jumbotron">
        <div class="container">
            <h1>{{ $cinema->location }}</h1>
            <p>{{ $cinema->city }}, {{ $cinema->country }} Cinema</p>
        </div>
    </div>

    <div class="container">

        {{--<img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""/>--}}
        @foreach ($moviesByRating as $rating => $movies)
            <div class="group">
                <span class="owl-circle {{ $rating }}">
                    <img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""/>
                </span>
                <h2><span class="{{ $rating }}"></span> {{ $rating }} Movies</h2>
                <p class="text-muted">Movies with {{ $rating }} Rotten Tomatoes and IMDB Ratings</p>
            </div>

            @foreach (array_chunk($movies, 4) as $movieRow)
                <div class="row">
                    @foreach ($movieRow as $movie)
                        <div class="col-xs-12 col-sm-3">
                            <div class="thumbnail">
                                <a href="{{ URL::to('cinemas/' . $cinema->slug . '/movies/' . $movie->slug . '/showings') }}">
                                    <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}">
                                </a>
                                <div class="caption {{ $rating }}">
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
    </div>

@stop
