<div class="thumbnail">
    <a href="{{ URL::to($url) }}">
        @if(!isset($movie->details) || !isset($movie->details->poster))
            <img src="{{ asset('images/no_poster.jpg') }}" alt="{{ $movie->title  }}">
        @else
            <img src="/{{ $movie->details->poster }}" alt="{{ $movie->title  }}">
        @endif
    </a>
    <div class="caption {{ $rating }}">
        <h4>
            <a href="{{ URL::to($url) }}">
                {{ $movie->title }}
            </a>
        </h4>
        @if ($movie->tomato_meter >= 0)
        <p>
            @if ($movie->tomato_meter > 75)
                <img src="{{ URL::asset('images/CF_240x240.png') }}" alt="" class="tomato-rating"/>
            @elseif ($movie->tomato_meter > 59)
                <img src="{{ URL::asset('images/fresh.png') }}" alt="" class="tomato-rating"/>
            @else
                <img src="{{ URL::asset('images/rotten.png') }}" alt="" class="tomato-rating" />
            @endif

            {{ $movie->tomato_meter }}%
        </p>
        @endif
    </div>
</div>
<p class="text-muted" style="margin-bottom: 30px;">
    @if(isset($movie->details))
    {{ str_limit($movie->details->synopsis , $limit = 150, $end = '...') }}
    @else
        No details available for this movie.
    @endif

    <a href="{{ URL::to($url) }}">read more</a>
</p>