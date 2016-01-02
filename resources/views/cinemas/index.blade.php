@extends('layouts.default')
@section('title', 'What\'s Good at the Movies - MoviesOwl')
@section('content')

    <div class="jumbotron">
        <div class="container">
            <h1>Find out what's Good at the Movies</h1>
{{--            <p>{{ $cinema->city }}, {{ $cinema->country }} Cinema</p>--}}
        </div>
    </div>

    <div class="container">

        @foreach ($cinemasByCity as $city => $cinemas)
            <h3>{{ $city }}</h3>
            <ul>
                @foreach ($cinemas as $cinema)
                    <li>
                        <a href="{{ URL::to('cinemas/' . $cinema->slug) }}">{{ $cinema->location }}</a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </div>

@stop