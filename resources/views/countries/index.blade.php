@extends('layouts.default')
@section('title', 'Movie Times, Reviews and Tickets at your Local Cinema')
@section('description', "Find Show times and Buy Tickets for movies at cinemas near you.")
@section('content')

    <div class="jumbotron">
        <div class="container">
            <h1>Movie Times, Reviews and Tickets at your Local Cinema</h1>
            <p>Find out what's Good at the Movies</p>
        </div>
    </div>

    <div class="container" style="margin-top:30px;">
        <h2 class="text-center">Select Your City to Find Movie Cinemas</h2>
        <p class="text-muted text-center">MoviesOwl helps you choose what to see at your local cinema by analysing movie review scores, public opinion and showtimes.</p>
        <div class="row">
            @foreach($countries as $country)
                <div class="col-sm-6">
                    <h3><a style="color: inherit" href="{{ url("/{$country['slug']}") }}">{{ $country['name'] }}</a></h3>
                    <ul>
                        @foreach ($country['cities'] as $city)
                        <li>
                            <a href="{{ url("cities/{$city}/today") }}">{{ $city }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
@stop