@extends('layouts.default')
@section('title', 'Movie Times, Reviews and Tickets at your Local Cinema - MoviesOwl')
@section('description', "All the movies on in " . ucfirst($cityName) . " cinemas {$day}")
@section('content')

    <div class="jumbotron">
        <div class="container">
            <h1>{{ ucfirst($cityName) }}</h1>
            <p>All the movies on in {{ ucfirst($cityName) }} Cinemas {{ $day }}.</p>

            <div class="btn-group" role="group" aria-label="..." style="margin: 10px 0;">
                <a href="{{ url("cities/{$cityName}/today") }}" class="btn btn-default time-button @if($day == 'today') active @endif">Today</a>
                <a href="{{ url("cities/{$cityName}/now") }}" class="btn btn-default time-button @if($day == 'now') active @endif">Now</a>
                @if(\Carbon\Carbon::now()->hour > 12)
                    <a href="{{ url("cities/{$cityName}/tomorrow") }}" class="btn btn-default time-button @if($day == 'tomorrow') active @endif">Tomorrow</a>
                @endif
            </div>
        </div>
    </div>

    <div class="container" style="margin-top:30px;">
        {{--<div class="well">--}}
        <h3 class="text-center">Cinemas</h3>
        @foreach (array_chunk($cinemasByLetter, 3) as $cinemaRow)
        <div class="row">
            @foreach ($cinemaRow as $cinemas)
            <div class="col-sm-4">
                <?php $firstCinema = $cinemas[0]; ?>
                <h3>{{ substr($firstCinema->location, 0, 1) }}</h3>
                <ul>
                    @foreach($cinemas as $cinema)
                    <li>
                        <a href="{{ url("{$cinema->slug}/today") }}">{{ $cinema->location }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <div class="container">
        @foreach ($moviesByRating as $rating => $movies)
            <div class="group">
                <span class="owl-circle {{ $rating }}">
                    <img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""/>
                </span>
                <h2><span class="{{ $rating }}"></span> {{ $rating }} Movies </h2>
                <p class="text-muted">Movies with {{ $rating }} Rotten Tomatoes reviews and IMDB Ratings</p>
            </div>

            @foreach (array_chunk($movies, 4) as $movieRow)
                <div class="row">
                    @foreach ($movieRow as $movie)
                        <div class="col-xs-6 col-sm-3">
                            @include('includes.movie-card', ["url" => 'movies/' . $movie->slug . '/' . $cityName . '/' . $day])
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endforeach
    </div>

@stop