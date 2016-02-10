@extends('layouts.default')
@section('title', 'Movie Times, Reviews and Tickets at your Local Cinema - MoviesOwl')
@section('content')

    <div class="jumbotron">
        <div class="container">
            <h1>Movie Times, Reviews and Tickets at your Local Cinema</h1>
            <p>Find out what's Good at the Movies</p>
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
                    <h3><a style="color: inherit" href="{{ url("cities/{$city}/today") }}">{{ $city }}</a></h3>
                    <ul>
                    @foreach ($cinemas as $cinema)
                        <li>

                            <a href="{{ url("{$cinema->slug}/today") }}">{{ $cinema->location }}</a>
                        </li>
                    @endforeach
                </ul>
                @endforeach
            </div>
        </div>
    </div>

@stop