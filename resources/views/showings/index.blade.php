@extends('layouts.default')
@section('title', 'Book Tickets ' . $movie->title . ' at ' .$cinema->location)
@section('content')

    <div class="jumbotron">
        <div class="container">
            <h1>{{ $cinema->location }}</h1>
            <p>{{ $cinema->city }}, {{ $cinema->country }} Cinema</p>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">
            </div>

            <div class="col-sm-8">
                <h3 style="margin-top:0;margin-bottom: 30px;">{{ $movie->title }}</h3>

                {{ $movie->synopsis }}

                <ul class="list-unstyled">
                    @foreach ($showings as $showing)
                        @include('includes.showing')
                        {{--<br/>--}}
                    @endforeach
                </ul>

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
