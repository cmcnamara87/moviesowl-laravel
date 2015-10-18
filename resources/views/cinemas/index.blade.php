@extends('layouts.default')
@section('title', 'What\'s Good at the Movies - MoviesOwl')
@section('content')

    <h1>MoviesOwl - Find out what's Good at the Movies</h1>
    <h2>Cinemas</h2>


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
@stop