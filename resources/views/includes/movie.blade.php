<div class="thumbnail {{ $movie->score() }}">
    <a href="{{ URL::to('movies/' . $movie->slug) }}">
        <img style="width:100%" src="{{ $movie->poster }}" alt="{{ $movie->title  }}">
    </a>
    <div class="caption">
        <h3>{{ $rank }}. {{ $movie->title }}</h3>
        <p>{{ $movie->run_time }} min - <span class="meter">{{ $movie->tomato_meter }}%</span></p>
    </div>
</div>


{{--<div class="panel panel-default">--}}
    {{--<div class="panel-body">--}}
        {{--<div class="row">--}}
            {{--<div class="col-sm-6">--}}
                {{--<a href="{{ URL::to('movies/' . $movie->slug) }}">--}}
                    {{--<img style="width:100%" src="{{ $movie->poster }}" alt="{{ $movie->title  }}">--}}
                {{--</a>--}}
            {{--</div>--}}
            {{--<div class="col-sm-6">--}}
                {{--<div>--}}
                    {{--<div class="muted">--}}
                        {{--2014--}}
                    {{--</div>--}}
                    {{--<h3>{{ $movie->title }}</h3>--}}
                    {{--<p>25 Jan 2015</p>--}}
                    {{--<p>{{ $movie->tomato_meter }}%</p>--}}
                    {{--<p>Action | Horror | Comedy</p>--}}
                {{--</div>--}}
                {{--<div class="text-muted">--}}
                    {{--{{ $movie->run_time }} min--}}
                {{--</div>--}}
                {{--<div>--}}
                    {{--{{ Str::words($movie->synopsis, 10) }} <a href="">Read More</a>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--</div>--}}
