@extends('layouts.default')
@section('title', 'What\'s Good at the Movies - MoviesOwl')
@section('content')

    <h2>MoviesOwl - Find out what's Good at the Movies</h2>

    <ul>
        @foreach ($cinemas as $cinema)
        <li>
            <a href="{{ URL::to('cinemas/' . $cinema->slug) }}">{{ $cinema->location }}</a>
        </li>
        @endforeach
    </ul>
@stop