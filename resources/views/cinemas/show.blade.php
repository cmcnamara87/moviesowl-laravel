@extends('layouts.default')
@section('title', $cinema->location . ' - Movie Times, Reviews and Tickets - MoviesOwl')
@section('content')

    @include('includes.cinema-jumbotron')

    <div class="container">

        @if(!count($movies))
            <div style="text-align: center">
                <p class="text-muted">
                    No more movies showing today.
                </p>
                <p>
                    <a  class="btn btn-primary btn-lg"  href="{{ URL::to("{$cinema->slug}/tomorrow") }}">
                        View Tomorrow
                    </a>
                </p>

                <div>
                    <img style="width:100%; max-width:250px;" src="{{ URL::asset('images/no-movies-owl.png') }}" alt=""/>
                </div>

            </div>

        @endif
        {{--<img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""/>--}}
        @foreach ($moviesByRating as $rating => $movies)
            <div class="group">
                <span class="owl-circle {{ $rating }}">
                    <img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""/>
                </span>
                <h2><span class="{{ $rating }}"></span> {{ $rating }} Movies </h2>
                <p class="text-muted">Movies at {{ $cinema->location }} with {{ $rating }} Rotten Tomatoes reviews and IMDB Ratings</p>
            </div>

            @foreach (array_chunk($movies, 4) as $movieRow)
                <div class="row">
                    @foreach ($movieRow as $movie)
                        <div class="col-xs-6 col-sm-3">
                            @include('includes.movie-card', ["url" => "{$cinema->slug}/{$movie->slug}/{$day}"])
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endforeach
    </div>

@stop
