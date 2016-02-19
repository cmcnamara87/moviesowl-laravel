@extends('layouts.default')
@section('title', "{$countryName} - Movie Times, Reviews and Tickets at your Local Cinema")
@section('description', "Find Show times and Buy Tickets for movies in {$countryName}.")
@section('content')

    <div class="jumbotron">
        <div class="container">
            <h1>Movie Times, Reviews and Tickets at your Local Cinema</h1>
            <p>Find out what's Good at the Movies</p>
        </div>
    </div>

    <div class="container" style="margin-top:30px;">
        <h3 class="text-center">Cinemas</h3>
        @foreach (array_chunk($cinemasByCity, 3) as $cinemaRow)
            <div class="row">
                @foreach ($cinemaRow as $cinemas)
                    <div class="col-sm-4">
                        <?php $firstCinema = $cinemas[0]; ?>
                        <h3><a style="color: inherit" href="{{ url("cities/{$firstCinema->city}/today") }}">{{ $firstCinema->city }}</a></h3>
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
@stop