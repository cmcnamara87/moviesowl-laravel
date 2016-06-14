@extends('layouts.default')
@section('title', "{$movie->title} - {$cinema->location} - Movie Times " . ucfirst($day) . ", Reviews and Tickets")
@section('description', "Find Show times and Buy Tickets for {$movie->title} at {$cinema->location}.")
@section('content')

<script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "Review",
  "itemReviewed": {
    "@type": "Movie",
    "name": "{{ $movie->title }}"
  },
  "reviewRating": {
    "@type": "Rating",
    "ratingValue": "{{ $movie->tomato_meter }}",
    "bestRating": "100"
  },
  "author": {
    "@type": "Organisation",
    "name": "MoviesOwl"
  },
  "publisher": {
    "@type": "Organization",
    "name": "MoviesOwl"
  }
}
</script>


    @include('includes.movie-jumbotron')

    <div class="breadcrumb-wrapper">
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="{{ url("cities/{$cinema->city}/{$day}") }}">{{ $cinema->city }}</a></li>
                <li><a href="{{ URL::to("{$cinema->slug}/{$day}") }}">{{ $cinema->location }}</a></li>
                <li class="active">{{ $movie->title }}</li>
                <li>{{ ucfirst($day) }}</li>
            </ol>
        </div>
    </div>

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [{
    "@type": "ListItem",
    "position": 1,
    "item": {
      "@id": "{{ url("cities/{$cinema->city}") }}",
      "name": "{{ $cinema->city }}"
    }
  },{
    "@type": "ListItem",
    "position": 2,
    "item": {
      "@id": "{{ url("{$cinema->slug}/{$day}") }}",
      "name": "{{ $cinema->location }}"
    }
  },{
    "@type": "ListItem",
    "position": 3,
    "item": {
      "@id": "{{ URL::current() }}",
      "name": "{{ $movie->title }}"
    }
  }]
}
</script>

    <div class="container" style="margin-top:30px;">
        <div class="row">
            <!-- poster -->
            <div class="col-sm-4 hidden-xs">
                <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }} at {{ $cinema->location }}" style="width:100%">
            </div>
            <!-- /poster -->

            <div class="col-sm-8">
                <h2 class="text-left" style="font-size:28px;margin-top:0;margin-bottom: 30px;">{{ $movie->title }}
                    <small>{{ $cinema->location }}</small>
                </h2>

                <div class="row">
                    <div class="col-sm-8">

                        @if($movie->details->trailer)
                            <div class="videoWrapper" style="margin-bottom: 20px;">
                                <!-- Copy & Pasted from YouTube -->
                                <iframe id="ytplayer" type="text/html" width="640" height="390"
                                        src="http://www.youtube.com/embed/{{ $movie->details->trailer }}?autoplay=0&origin=http://moviesowl.com"
                                        frameborder="0"></iframe>
                            </div>
                        @endif
                        <p style="margin-bottom: 30px">
                            {{ $movie->details->synopsis }}
                        </p>

                        <div class="panel panel-default" style="margin-bottom: 40px;">
                            <div class="panel-body">
                                <div class="media">
                                    <div class="pull-left">
                                        <img class="owl" src="{{ URL::asset('images/owl.png') }}" alt="" style="margin-top:10px;margin-left:10px;margin-right: 10px;"/>
                                    </div>
                                    <div class="media-body">
                                        <h5>Our Review</h5>
                                        <p>
                                            @if ($movie->tomato_meter > 75) A great movie! Critically loved. Definitely worth checking out! @elseif ($movie->tomato_meter > 59) Looks pretty good, check it out if it looks like something you'd like. @else It's not high art, but you might still enjoy it. @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <h4 style="margin-bottom: 30px;font-weight:200" >Pick a Time</h4>
                            @foreach ($showingsByTime as $timeOfDay => $showings)
                                @if (count($showings))
                                    <h5 class="text-capitalize text-muted" style="font-weight:200;margin-bottom: 16px;">
                                        @if($timeOfDay == 'evening')
                                            <i class="fa fa-moon-o"></i>
                                        @elseif($timeOfDay == 'morning')
                                            <i class="fa fa-coffee"></i>
                                        @else
                                            <i class="fa fa-sun-o"></i>
                                        @endif
                                        {{ $timeOfDay }} Sessions
                                    </h5>
{{--                                    @foreach (array_chunk($showings, 2) as $showingsRow)--}}
                                        {{--<div class="row">--}}
                                    @foreach ($showings as $showing)
                                        {{--<div class="col-sm-6">--}}
                                            @include('includes.showing')
                                        {{--</div>--}}
                                    @endforeach
                                        {{--</div>--}}
                                    {{--@endforeach--}}
                                @endif
                            @endforeach
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

                        <!-- ad MoviesOwl Movie Deatils -->
{{--                        @if($cinema->country != "Australia")--}}
                        <div style="margin-top:40px;">
                            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                            <!-- MoviesOwl Movie Deatils -->
                            <ins class="adsbygoogle"
                                 style="display:block"
                                 data-ad-client="ca-pub-8017658135166310"
                                 data-ad-slot="9723953112"
                                 data-ad-format="auto"></ins>
                            <script>
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            </script>
                        </div>
                        <!-- /ad MoviesOwl Movie Deatils -->
                        {{--@endif--}}

                    </div>
                </div>
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
