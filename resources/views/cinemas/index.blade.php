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
        <?php $count = 0; ?>
        <div class="row">
            <div class="col-sm-4">
                @foreach ($cinemasByCity as $city => $cinemas)
                    <?php $count += 1; ?>
                    @if ($count % (count($cinemasByCity) / 3) == 0)
                        </div><div class="col-sm-4">
                    @endif
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
        </div>
    </div>

@stop