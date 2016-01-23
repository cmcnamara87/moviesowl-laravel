<div class="jumbotron">
    <div class="container">
        <h1>{{ $movie->title }} at {{ $cinema->location }}</h1>
        <p>Movie Times, Reviews and Tickets for {{ $startingAfter->format('l jS \\of F Y') }} - {{ $cinema->city }}, {{ $cinema->country }} Cinema</p>
    </div>
</div>