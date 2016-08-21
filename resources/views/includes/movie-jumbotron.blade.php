<div class="jumbotron">
    <div class="container">
        <h1><strong>{{ $movie->title }}</strong>@if(isset($cinema)) at <strong>{{ $cinema->location }}</strong>@endif @if(isset($cityName)) in <strong>{{ $cityName }}</strong> @endif</h1>
        <p class="hidden-xs">Movie Times, Reviews and Tickets for {{ \Carbon\Carbon::$day()->format('l jS \\of F Y') }} - @if(isset($cinema)){{ $cinema->city }}, {{ $cinema->country }} Cinema @endif @if(isset($cityName)) {{ $cityName }} @endif</p>
    </div>
</div>