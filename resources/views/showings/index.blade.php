@extends('layouts.default')
@section('title', 'Book Tickets ' . $movie->title . ' at ' .$cinema->location)
@section('content')

    <h1>Book Tickets for {{ $movie->title }} at {{ $cinema->location }}</h1>

    <div class="row">
        <div class="col-sm-3">
            <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">
        </div>
        <div class="col-sm-6">
            {{ $movie->synopsis }}

            <h2>Sessions</h2>
            <ul>
                @foreach ($showings as $showing)
                    <li>
                        <a href="{{ URL::to('showings/'. $showing->id) }}">{{ $showing->start_time->toTimeString() }}</a>
                        {{ $showing->showing_type }}
                        {{ $showing->screen_type }}
                        {{ $showing->cinema_size }}
                        {{ $showing->percent_full }}% Full
                    </li>
                @endforeach
            </ul>

            {{--<a class="btn btn-default" href="{{ URL::to('movies/' . $movie->id) }}">Find other cinemas</a>--}}


        </div>
        <div class="col-sm-3">
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
@stop
