<div class="jumbotron">
    <div class="container">
        <h1>{{ $cinema->location }}</h1>
        <p class="hidden-xs">Movie Times, Reviews and Tickets for {{ \Carbon\Carbon::$day()->format('l jS \\of F Y') }} - {{ $cinema->city }}, {{ $cinema->country }} Cinema</p>

        <div class="btn-group" role="group" aria-label="..." style="margin: 10px 0;">
            <a href="{{ url("{$cinema->slug}/today") }}" class="btn btn-default time-button @if($day == 'today') active @endif">Today</a>
            <a href="{{ url("{$cinema->slug}/now") }}" class="btn btn-default time-button @if($day == 'now') active @endif">Now</a>
            @if(\Carbon\Carbon::now()->hour > 12)
            <a href="{{ url("{$cinema->slug}/tomorrow") }}" class="btn btn-default time-button @if($day == 'tomorrow') active @endif">Tomorrow</a>
            @endif
        </div>
    </div>
</div>