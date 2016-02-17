<div class="jumbotron">
    <div class="container">
        <h1>{{ $movie->title }} at {{ $cinema->location }}</h1>
        <p class="hidden-xs">Movie Times, Reviews and Tickets for {{ \Carbon\Carbon::$day()->format('l jS \\of F Y') }} - {{ $cinema->city }}, {{ $cinema->country }} Cinema</p>
    </div>
</div>