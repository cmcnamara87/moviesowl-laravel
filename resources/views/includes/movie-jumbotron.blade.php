<div class="jumbotron">
    <div class="container">
        <h1>{{ $movie->title }}@if(isset($cinema)) at {{ $cinema->location }}@endif @if(isset($cityName)) in {{ $cityName }} @endif</h1>
        <p class="hidden-xs">Movie Times, Reviews and Tickets for {{ \Carbon\Carbon::$day()->format('l jS \\of F Y') }} - @if(isset($cinema)){{ $cinema->city }}, {{ $cinema->country }} Cinema @endif @if(isset($cityName)) {{ $cityName }} @endif</p>
    </div>
</div>