@extends('layouts.default')
@section('title', 'Book Tickets ' . $movie->title . ' at ' .$cinema->location)
@section('content')

    @include('includes.cinema-jumbotron')

    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{{ URL::to('cinemas/') }}">{{ $cinema->city }}</a></li>
            <li><a href="{{ URL::to('cinemas/' . $cinema->slug . '?starting_after=' . $startingAfter->timestamp) }}">{{ $cinema->location }}</a></li>
            <li class="active">{{ $movie->title }}</li>
        </ol>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">
            </div>

            <div class="col-sm-8">
                <h3 style="margin-top:0;margin-bottom: 30px;">{{ $movie->title }}</h3>

                {{ $movie->synopsis }}

                @foreach ($showingsByTime as $timeOfDay => $showings)
                <h4 class="text-uppercase text-muted" style="font-size:14px;margin-bottom: 24px;">{{ $timeOfDay }}</h4>
                <ul class="list-unstyled">

                        @foreach ($showings as $showing)
                            @include('includes.showing')
                        @endforeach
                </ul>
                @endforeach

                {{--<a class="btn btn-default" href="{{ URL::to('movies/' . $movie->id) }}">Find other cinemas</a>--}}


            </div>
            <div class="col-sm-4">
                <dl>
                    <dt>
                        Rotten Tomatoes
                    </dt>
                    <dd>
                        {{ $movie->tomato_meter }}%
                    </dd>

                    <dt>
                        Run Time
                    </dt>
                    <dd>
                        {{ $movie->run_time }} minutes
                    </dd>

                    <dt>
                        Cast
                    </dt>
                    <dd>
                        {{ $movie->cast }}
                    </dd>
                </dl>
            </div>

        </div>

    </div>
@stop
