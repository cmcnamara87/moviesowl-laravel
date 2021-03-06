@extends('layouts.default')
@section('title', "{$movie->title} - {$cityName} - " . "Movie Times " . ucfirst($day) . ", Reviews and Tickets")
@section('description', "Find Show times and Buy Tickets for {$movie->title} in {$cityName}.")
@section('content')

    @include('includes.movie-jumbotron')

    <div class="breadcrumb-wrapper" >
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="{{ url("cities/{$cityName}/{$day}") }}">{{ ucfirst($cityName) }}</a></li>
                <li>{{ ucfirst($day) }}</li>
                <li class="active">{{ $movie->title }}</li>
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
      "@id": "{{ url("cities/{$cityName}/{$day}") }}",
      "name": "{{ ucfirst($cityName) }}"
    }
  },{
    "@type": "ListItem",
    "position": 2,
    "item": {
      "@id": "{{ url("cities/{$cityName}/{$day}") }}",
      "name": "{{ ucfirst($day) }}"
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
            <div class="col-sm-4 hidden-xs">
                @if(isset($movie->details->poster))
                <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}" style="width:100%">
                @endif
            </div>

            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-8 col-xs-12">
                        <h2 style="text-align: left;font-size: 24px;margin-top: 0;font-weight: bold;">Cinemas</h2>
                        <ul class="list-unstyled">
                            @if(!count($cinemas))
                           <p><strong>No sessions at any cinema {{ $day }}. </strong></p>

                            @endif
                        @foreach ($cinemas as $cinema)
                            <li>
                                <a href="{{ URL::to("{$cinema->slug}/{$day}") }}">
                                    <strong>{{ $cinema->location }}</strong></a>
                                <p class="text-muted">
                                    @foreach($cinema->showings as $showing)
                                        <a href="{{ URL::to("{$cinema->slug}/{$movie->slug}/{$day}") }}" class="text-muted">
                                            {{ $showing->start_time->format('h:i A') }}</a>@if($showing != $cinema->showings->last()),@endif
                                    @endforeach
                                </p>

                            </li>
                        @endforeach
                        </ul>

                        <div style="margin-top:30px;padding-top:30px;border-top:1px solid #ddd">
                            <p style="margin-bottom: 40px;">
                                @if(isset($movie->details->synopsis))
                                    {{ $movie->details->synopsis }}
                                @else
                                    No synopsis available.
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <dl>
                            @if(isset($movie->details))
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
                            @endif
                        </dl>

                        <!-- ad MoviesOwl Movie Details -->
{{--                        @if($country != 'Australia')--}}
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
                        {{--@endif--}}
                        <!-- /ad MoviesOwl Movie Details -->
                    </div>
                </div>

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

        mixpanel.track("Viewed movie");
    </script>
@stop
