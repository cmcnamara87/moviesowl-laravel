@extends('layouts.default')
@section('title', "{$movie->title} - {$cinema->location} - Movie Times " . ucfirst($day) . ", Reviews and Tickets")
@section('description', "Find Show times and Buy Tickets for {$movie->title} at {$cinema->location}.")
@section('content')

    @if ($movie->tomato_meter > 0)
        <script type="application/ld+json">
{
  "@context": "http://schema.org/",
  "@type": "Review",
  "datePublished": "{{ \Carbon\Carbon::now()->toIso8601String() }}",
  "description": "@if ($movie->tomato_meter > 75) {{ ucwords(strtolower($movie->title)) }}
            is a great movie! Critically loved and definitely worth checking out while its still at the cinemas! @elseif ($movie->tomato_meter > 59) {{ ucwords(strtolower($movie->title)) }}
            looks pretty good, but has mixed reviews, check it out if it looks like something you'd like. @else
                Although {{ ucwords(strtolower($movie->title)) }}
                is getting generally unfavorable reviews, you should check it out if its something you are interested in. @endif",
  "reviewBody": "@if ($movie->tomato_meter > 75) {{ ucwords(strtolower($movie->title)) }}
            is a great movie! Loved by critics and audiences alike, definitely worth checking out while its still at the cinemas! @elseif ($movie->tomato_meter > 59) {{ ucwords(strtolower($movie->title)) }}
            looks pretty good, but has mixed reviews, check it out if it looks like something you'd like. @else
                Although {{ ucwords(strtolower($movie->title)) }}
                is getting generally unfavorable reviews, you should check it out if its something you are interested in. @endif",
  "itemReviewed": {
    "@type": "Movie",
    "name": "{{ $movie->title }}",
    "image": "http://moviesowl.com/{{ $movie->details->poster }}",
    "sameAs": "http://imdb.com/title/{{ $movie->imdb_id }}",
    "director": "{{ $movie->details->director }}",
    "dateCreated": "{{ $movie->created_at->toIso8601String() }}"
  },
  "reviewRating": {
    "@type": "Rating",
    "ratingValue": "{{ $movie->tomato_meter }}",
    "bestRating": "100"
  },
  "author": {
    "name": "Craig McNamara",
    "sameAs": "https://twitter.com/cmcnamara87"
  },
  "publisher": {
    "@type": "Organization",
    "name": "MoviesOwl"
  }
}


        </script>
    @endif


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
        <div style="margin-bottom: 30px;">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- MoviesOwl Top -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-8017658135166310"
                 data-ad-slot="8080263912"
                 data-ad-format="auto"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>

        <div class="row">
            <!-- poster -->
            <div class="col-sm-4 hidden-xs">
                <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }} at {{ $cinema->location }}"
                     style="width:100%">
            </div>
            <!-- /poster -->

            <div class="col-sm-8">
                <h2 class="text-left" style="font-size:28px;margin-top:0;margin-bottom: 10px;">
                    {{ $cinema->location }}
                    <a href="{{ url("movies/{$movie->slug}/{$cinema->city}/{$day}") }}">
                        <small>(Change Cinema)</small>
                    </a>
                </h2>

                <div class="row">
                    <div class="col-sm-8">
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

                        @if(!$showingCount)
                            <p><strong>No sessions for {{ $day }}.</strong></p>
                        @endif
                        <a href="{{ url("movies/{$movie->slug}/{$cinema->city}/{$day}") }}">Find
                            <strong>{{ $movie->title }}</strong> in {{ $cinema->city }}</a>

                        <!-- details -->
                        <div style="margin-top:30px;border-top:1px solid #ddd;padding-top:40px">
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

                            @if($movie->tomato_meter > 0)
                                <div class="panel panel-default" style="margin-bottom: 40px;">
                                    <div class="panel-body">
                                        <div class="media">
                                            <div class="pull-left">
                                                <img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""
                                                     style="margin-top:10px;margin-left:10px;margin-right: 10px;"/>
                                            </div>
                                            <div class="media-body">
                                                <h5>Our Verdict</h5>
                                                <p>
                                                    @if ($movie->tomato_meter > 75) {{ ucwords(strtolower($movie->title)) }}
                                                    is a great movie! Loved by critics and audiences alike, definitely worth
                                                    checking out while its still at the
                                                    cinemas! @elseif ($movie->tomato_meter > 59) {{ ucwords(strtolower($movie->title)) }}
                                                    looks pretty good, but has mixed reviews, check it out if it looks like
                                                    something you'd like. @else
                                                        Although {{ ucwords(strtolower($movie->title)) }} is getting
                                                        generally unfavorable reviews, you should check it out if its
                                                        something you are interested in. @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- details -->
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

                <div class="row">
                    <div class="col-sm-8">

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

        mixpanel.track("Viewed movie + cinema session times");
    </script>
@stop
