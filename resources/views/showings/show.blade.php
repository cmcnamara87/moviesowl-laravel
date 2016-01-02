@extends('layouts.default')
@section('title', 'Book Tickets ' . $movie->title . ' at ' .
 $showing->start_time->toDateTimeString() . ' at ' .  $cinema->location)
@section('content')



    {{--<div class="container">--}}
    {{--<div class="row">--}}
    {{--<div class="col-sm-4">--}}
    {{--<img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">--}}
    {{--</div>--}}

    {{--<div class="col-sm-8">--}}
    {{--<h3 style="margin-top:0;margin-bottom: 30px;">Book Tickets for {{ $movie->title }}</h3>--}}

    {{--{{ $movie->synopsis }}--}}

    {{--<ul class="list-unstyled">--}}
    {{--@foreach ($showings as $showing)--}}
    {{--@include('includes.showing')--}}
    {{--<br/>--}}
    {{--@endforeach--}}
    {{--</ul>--}}

    {{--<a class="btn btn-default" href="{{ URL::to('movies/' . $movie->id) }}">Find other cinemas</a>--}}


    {{--</div>--}}
    {{--<div class="col-sm-4">--}}
    {{--<dl>--}}
    {{--<dt>--}}
    {{--Rotten Tomatoes--}}
    {{--</dt>--}}
    {{--<dd>--}}
    {{--{{ $movie->tomato_meter }}%--}}
    {{--</dd>--}}

    {{--<dt>--}}
    {{--Run Time--}}
    {{--</dt>--}}
    {{--<dd>--}}
    {{--{{ $movie->run_time }} minutes--}}
    {{--</dd>--}}

    {{--<dt>--}}
    {{--Cast--}}
    {{--</dt>--}}
    {{--<dd>--}}
    {{--{{ $movie->cast }}--}}
    {{--</dd>--}}
    {{--</dl>--}}
    {{--</div>--}}

    {{--</div>--}}

    {{--</div>--}}
    {{----}}

    <div class="jumbotron">
        <div class="container">
            <h1>{{ $cinema->location }}</h1>
            <p>{{ $cinema->city }}, {{ $cinema->country }} Cinema</p>
        </div>
    </div>

    <div class="container">
        @if($showing->seats)
            <div style="padding: 48px;border:1px solid #ddd;text-align: center;">
                <div class="row">
                    <div class="col-sm-4">
                        <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">
                    </div>
                    <div class="col-sm-8">
                        <h3 style="margin-top:0;margin-bottom: 10px;">
                            <a href="{{ URL::to('cinemas/'.$cinema->id .'/movies/' . $movie->id .'/showings') }}">
                                {{ $movie->title }}
                            </a>
                        </h3>

                        <p class="text-muted"
                           style="text-transform: uppercase;font-size:12px;margin-bottom:20px;">

                            {{ $showing->start_time->format('h:i A') }}

                            @if($showing->screen_type != "standard")
                                {{ $showing->screen_type }}
                            @endif
                            @if($showing->showing_type != "standard")

                                {{ $showing->showing_type }}
                            @endif
                            {{ $showing->cinema_size }} Cinema
                            {{ $showing->percent_full }}% Full
                        </p>

                        <div style="margin-bottom: 30px;">
                            <a class="btn btn-lg btn-primary" href="{{ $showing->tickets_url }}">
                                Buy Tickets <i class="fa fa-external-link"></i>
                            </a>
                        </div>

                        <div style="text-align:center;margin-bottom:24px;">
                            Front of Cinema
                        </div>
                        @foreach ($showing->seats as $seatRow)
                            <div>
                                @foreach ($seatRow as $seat)
                                    <div style="position:relative;float:left;width:<?php echo 100 / count($seatRow); ?>%; padding-bottom:<?php echo 100 / count($seatRow); ?>%;">
                                        <div style="position:absolute;top:0;left:0;right:2px;bottom:2px;background-color:<?php if ($seat == 'taken') echo 'red'; elseif ($seat == 'available') echo 'grey';
                                        else echo 'transparent';?>"></div>
                                    </div>@endforeach
                            </div>
                        @endforeach
                        <div style="text-align:center;">
                            Rear of Cinema
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>


@stop
