@extends('layouts.default')
@section('title', 'What\'s Good at the Movies - MoviesOwl')
@section('content')

    {{--<h1>What's Good at the Movies</h1>--}}

    @if(isset($topMovie))
    <div class="jumbotron">
        <div class="media">
            <div class="media-left">
                <a href="{{ URL::route('movies.show', array($topMovie->id)) }}">
                    <img class="media-object" src="{{ $topMovie->poster }}" alt="...">
                </a>
            </div>
            <div class="media-body">
                <h2 class="media-heading">{{ $topMovie->title }}</h2>
                <p>
                    {{ $topMovie->synopsis }}
                </p>
                <p>
                    <a class="btn btn-primary btn-lg" href="{{ URL::route('movies.show', array($topMovie->id)) }}">
                        Find Sessions
                    </a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <div>
        @foreach($cinemas as $cinema)
            <a href="{{ route('cinemas.show', $cinema->slug) }}" class="btn btn-default" style="border-radius:100px">
                {{ $cinema->location }}
            </a>
        @endforeach
    </div>
    @if($movies->count())
    <h2>Top {{ $movies->count() }} Movies You Can See Right Now</h2>
    <p>
        All Cinemas <a href="{{ route('cinemas.index') }}">Select a cinema</a>
    </p>
    @include('includes.movies-grid', ['movies' => $movies->take(6)])
    @endif

    <h2>Top {{ $moviesNewThisWeek->count() }} Movies New This Week</h2>
    <p>
        All Cinemas <a href="{{ route('cinemas.index') }}">Select a cinema</a>
    </p>
    @include('includes.movies-grid', ['movies' => $moviesNewThisWeek->take(6)])

    <h2>Top {{ $moviesCriticalAcclaim->count() }} Critically Acclaimed This Week</h2>
    <p>
        All Cinemas <a href="{{ route('cinemas.index') }}">Select a cinema</a>
    </p>
    @include('includes.movies-grid', ['movies' => $moviesCriticalAcclaim->take(6)])

    {{--<h2>Last Chance</h2>--}}
    {{--@include('includes.movies-grid')--}}

@stop