@extends('layouts.default')
@section('title', $movie->title . ' at ' .
 $showing->start_time->format('l jS \\of F Y h:i: A') . ' - ' .  $cinema->location . ' - Movie Times, Reviews and Tickets')
@section('description', 'Book tickets for ' . $showing->start_time->format('l jS \\of F Y h:i: A') . ' ' . $movie->title . ' at ' .$cinema->location)
@section('content')

    <div class="hidden-xs">
        @include('includes.movie-jumbotron')
    </div>


    <div class="breadcrumb-wrapper">
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="{{ url("cities/{$cinema->city}/{$day}") }}">{{ $cinema->city }}</a></li>
                <li><a href="{{ URL::to("{$cinema->slug}/{$day}") }}">{{ $cinema->location }}</a></li>
                <li>
                    <a href="{{ URL::to("{$cinema->slug}/{$movie->slug}/{$day}") }}">
                        {{ $movie->title }}
                    </a>
                </li>
                <li>{{ ucfirst($day) }}</li>
                <li class="active">{{ $showing->start_time->format('h:i A')  }}</li>
            </ol>
        </div>
    </div>

    <div class="container">
        <ol class="breadcrumb">

        </ol>
    </div>

    <!-- MoviesOwl Showing ad -->
    @if($cinema->country != "Australia")
    <div style="margin-top:30px;">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- MoviesOwl Showing -->
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="ca-pub-8017658135166310"
             data-ad-slot="6491285118"
             data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    @endif
    <!-- /MoviesOwl Showing ad -->

    <div class="container">
        <div class="panel panel-default">
            <ul class="list-group">
                <li class="list-group-item text-center" style="padding:20px 0">
                    <h3 style="margin-top:0;">
                        {{ $movie->title }}
                    </h3>

                    <p>
                        <strong>{{ $showing->cinema->location }}</strong>
                    </p>

                    <p>
                        <strong class="text-muted">{{ $showing->start_time->format('h:i A') }}</strong>
                    </p>
                    @if($showing->screen_type != "standard")
                        <p>
                            {{ $showing->screen_type }}
                        </p>
                    @endif
                    @if($showing->showing_type != "standard")
                        <p>
                            {{ $showing->showing_type }}
                        </p>
                    @endif
                    <div>
                        <a class="btn btn-lg btn-primary" target="_blank"
                           style="margin-bottom: 15px;"
                           href="@if(strlen($showing->tickets_url)){{ $showing->tickets_url }}@else{{ $showing->cinema->homepage_url }}@endif"
                           onclick="ga('send', 'event', 'button', 'buy_tickets');">
                            Buy Tickets <i class="fa fa-external-link"></i>
                        </a>
                        @if(strpos($showing->tickets_url, 'fandango'))
                        <p class="text-muted">
                            Processed by Fandango. It may take a few seconds to connect.
                        </p>
                        @endif
                    </div>
                </li>
            </ul>
            <img class="img-responsive visible-xs" src="{{ asset($movie->details->wide_poster) }}" alt=""/>
            @if($showing->seats)
                <div class="panel-body text-center">
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-4">
                            <h5>Cinema Information</h5>

                            <p>{{ ucfirst($showing->cinema_size) }} size Cinema</p>

                            <div class="text-center text-muted text-uppercase"
                                 style="text-align:center;margin-bottom:24px;">
                                <small>Front of Cinema</small>
                            </div>
                            @foreach ($showing->seats as $seatRow)
                                <div>
                                    @foreach ($seatRow as $seat)
                                        <div style="position:relative;float:left;width:<?php echo 100 / count($seatRow); ?>%; padding-bottom:<?php echo 100 / count($seatRow); ?>%;">
                                            <div style="position:absolute;top:0;left:0;right:2px;bottom:2px;background-color:<?php if ($seat == 'taken') echo '#F76394'; elseif ($seat == 'available') echo 'grey';
                                            else echo 'transparent';?>"></div>
                                        </div>@endforeach
                                </div>
                            @endforeach
                            <div class="text-center text-muted text-uppercase">
                                <small>Rear of Cinema</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>


@stop
