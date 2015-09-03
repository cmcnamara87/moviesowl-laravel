@extends('layouts.default')
@section('title', $movie->title)
@section('content')

    <h1>{{ $movie->title }}</h1>

    <div class="row">
        <div class="col-sm-3">
            <img src="{{ $movie->poster }}" alt="{{ $movie->title  }}" style="width:100%">
        </div>
        <div class="col-sm-6">
            {{ $movie->synopsis }}

            <h2>Cinemas</h2>
            <ul>
                @foreach ($cinemas as $cinema)
                <li>
                    <a href="{{ URL::to('cinemas/' . $cinema->slug . '/movies/' . $movie->slug . '/showings') }}">
                        {{ $cinema->location }}
                    </a>

                </li>
                @endforeach
            </ul>


        </div>
        <div class="col-sm-3">
            <dl>
                <dt>
                    Rotten Tomatoes
                </dt>
                <dd>
                    {{ $movie->tomato_meter }}
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