@extends('layouts.default')
@section('title', "{$cinema->location} - Movie Times " . ucfirst($day) . ", Reviews and Tickets")
@section('description', "Find " . ucfirst($day) . "'sMovie Times, Reviews and Tickets at {$cinema->location}.")
@section('content')

    @include('includes.cinema-jumbotron')

    <div class="breadcrumb-wrapper" >
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="{{ url("cities/{$cinema->city}/{$day}") }}">{{ ucfirst($cinema->city) }}</a></li>
                <li class="active">{{ $cinema->location }}</li>
                <li class="active">{{ ucfirst($day) }}</li>
            </ol>
        </div>
    </div>

    <div class="container">
        <div style="margin-top:30px;margin-bottom: 30px;">
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
        
        @if(!count($movies))
            <div style="text-align: center;margin-top:30px;">
                <p class="text-muted">
                    No more movies showing today.
                </p>
                <p>
                    <a  class="btn btn-primary btn-lg"  href="{{ URL::to("{$cinema->slug}/tomorrow") }}">
                        View Tomorrow
                    </a>
                </p>

                <div>
                    <img style="width:100%; max-width:250px;" src="{{ URL::asset('images/no-movies-owl.png') }}" alt=""/>
                </div>

            </div>
        @endif
        {{--<img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""/>--}}
        @foreach ($moviesByRating as $rating => $movies)
            <div class="group">
                <span class="owl-circle {{ $rating }}">
                    <img class="owl" src="{{ URL::asset('images/owl.png') }}" alt=""/>
                </span>
                <h2><span class="{{ $rating }}"></span> {{ $rating }} Movies </h2>
                <p class="text-muted">Movies at {{ $cinema->location }} with {{ $rating }} Rotten Tomatoes reviews and IMDB Ratings</p>
            </div>

            @foreach (array_chunk($movies, 4) as $movieRow)
                <div class="row">
                    @foreach ($movieRow as $movie)
                        <div class="col-xs-6 col-sm-3">
                            @include('includes.movie-card', ["url" => "{$cinema->slug}/{$movie->slug}/{$day}"])
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endforeach
    </div>

@stop
