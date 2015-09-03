@foreach (array_chunk($movies->all(), 6) as $movieRow)
    <div class="row">
        @foreach ($movieRow as $index => $movie)
            <div class="col-xs-12 col-sm-2">
                @include('includes.movie', array('rank' => $index + 1))
            </div>
        @endforeach
    </div>
@endforeach