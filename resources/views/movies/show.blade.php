@extends('layouts.default')
@section('title', $movie->title)
@section('content')

    <div class="breadcrumb-wrapper" >
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="{{ url("cities/{$cityName}/{$day}") }}">{{ ucfirst($cityName) }}</a></li>
                <li>{{ ucfirst($day) }}</li>
                <li class="active">{{ $movie->title }}</li>
            </ol>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">
            </div>

            <div class="col-sm-8">
                <h3 style="margin-top:0;margin-bottom: 30px;">{{ $movie->title }}</h3>

                <div class="row">
                    <div class="col-sm-8">
                        <p style="margin-bottom: 40px;">
                            {{ $movie->details->synopsis }}
                        </p>
                    </div>
                    <div class="col-sm-4">
                        <dl>
                            <dt class="text-muted" style="font-weight: normal;font-size:12px;text-transform: uppercase">
                                Rotten Tomatoes
                            </dt>
                            <dd>
                                @if ($movie->tomato_meter > 75)
                                    <img src="/images/CF_240x240.png" alt="" class="tomato-rating" style="width:20px;"/>
                                @elseif ($movie->tomato_meter > 59)
                                    <img src="/images/fresh.png" alt="" class="tomato-rating" style="width:20px;"/>
                                @else
                                    <img src="/images/rotten.png" alt="" class="tomato-rating" style="width:20px;"/>
                                @endif
                                {{ $movie->tomato_meter }}%
                            </dd>

                            <dt class="text-muted"
                                style="margin-top:20px;font-weight: normal;font-size:12px;text-transform: uppercase">
                                Run Time
                            </dt>
                            <dd>
                                {{ $movie->details->run_time }} minutes
                            </dd>

                            <dt class="text-muted"
                                style="margin-top:20px;font-weight: normal;font-size:12px;text-transform: uppercase">
                                Cast
                            </dt>
                            <dd>
                                {{ $movie->details->cast }}
                            </dd>
                        </dl>
                    </div>
                </div>

                @foreach ($cinemasByCity as $city => $cinemas)
                    <h5>{{ $city }}</h5>
                    <ul>
                        @foreach ($cinemas as $cinema)
                            <li>
                                <a href="{{ URL::to("{$cinema->slug}/{$movie->slug}/{$day}") }}">
                                    {{ $movie->title}} at {{ $cinema->location }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach

                {{--<a class="btn btn-default" href="{{ URL::to('movies/' . $movie->id) }}">Find other cinemas</a>--}}


            </div>

        </div>

    </div>

    <script>

        $('.time').each(function () {
            // get showing id
            var $time = $(this);
            var showingId = $time.attr('data-showing-id');
            $.get('/api/v1/showings/' + showingId, function (data) {
                $time.find('.time__type').text(data.cinema_size + ' cinema');

                if (data.percent_full > 0) {
                    $time.find('.progress-bar').css('width', (data.percent_full) + '%')
                            .text(Math.round(data.percent_full) + '% Full');
                } else {
                    $time.find('.progress').hide();
                }

            });
        });

    </script>
@stop
