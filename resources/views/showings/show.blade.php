@extends('layouts.default')
@section('title', 'Book Tickets ' . $movie->title . ' at ' .
 $showing->start_time->toDateTimeString() . ' at ' .  $cinema->location)
@section('content')

    <h1>Book Tickets {{ $movie->title }} at {{ $showing->start_time->toTimeString() }} at {{ $cinema->location }}</h1>

    <div class="row">
        <div class="col-sm-3">
            <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">
        </div>
        <div class="col-sm-6">
            {{ $movie->synopsis }}

            <h2>Session</h2>
            <div>
                <a class="btn btn-primary" href="{{ $showing->tickets_url }}">Buy Tickets</a>
                <a class="btn btn-default" href="{{ URL::to('cinemas/'.$cinema->id .'/movies/' . $movie->id .'/showings') }}">Find other sessions</a>
            </div>
            <div>
                <div style="text-align:center;">
                    Front of Cinema
                </div>
                @foreach ($showing->seats as $seatRow)
                    <div>
                        @foreach ($seatRow as $seat)<div style="position:relative;float:left;width:<?php echo 100 / count($seatRow); ?>%; padding-bottom:<?php echo 100 / count($seatRow); ?>%;">
                            <div style="position:absolute;top:0;left:0;right:2px;bottom:2px;background-color:<?php if($seat=='taken') echo 'red'; elseif($seat=='available') echo 'grey'; else echo 'white';?>"></div>
                        </div>@endforeach
                    </div>
                @endforeach
                <div style="text-align:center;">
                    Rear of Cinema
                </div>
            </div>


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
