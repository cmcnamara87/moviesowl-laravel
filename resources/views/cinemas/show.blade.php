@extends('layouts.default')
@section('title', $cinema->location . ' - What\'s Good at the Movies - MoviesOwl')
@section('content')

    @include('includes.cinema-jumbotron')

    <div class="container">

        @if(!count($movies))
            <div style="text-align: center">
                <p class="text-muted">
                    No more movies showing today.
                </p>
                <p>
                    <a  class="btn btn-primary btn-lg"  href="{{ URL::to('cinemas/' . $cinema->slug . '?starting_after=' . \Carbon\Carbon::tomorrow()->timestamp) }}">
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
                <h2><span class="{{ $rating }}"></span> {{ $rating }} Movies</h2>
                <p class="text-muted">Movies with {{ $rating }} Rotten Tomatoes and IMDB Ratings</p>
            </div>

            @foreach (array_chunk($movies, 4) as $movieRow)
                <div class="row">
                    @foreach ($movieRow as $movie)
                        <div class="col-xs-12 col-sm-3">
                            <div class="thumbnail">
                                <a href="{{ URL::to('cinemas/' . $cinema->slug . '/movies/' . $movie->slug . '/showings?starting_after=' . $startingAfter->timestamp) }}">
                                    <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}">
                                </a>
                                <div class="caption {{ $rating }}">
                                    <h4>{{ $movie->title }}</h4>
                                    <p>
                                        @if ($movie->tomato_meter > 75)
                                            <img src="/images/CF_240x240.png" alt="" class="tomato-rating"/>
                                        @elseif ($movie->tomato_meter > 59)
                                            <img src="/images/fresh.png" alt="" class="tomato-rating"/>
                                        @else
                                            <img src="/images/rotten.png" alt="" class="tomato-rating" />
                                        @endif
                                        {{ $movie->tomato_meter }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endforeach
    </div>

@stop
