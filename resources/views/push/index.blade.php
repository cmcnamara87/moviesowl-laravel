@extends('layouts.default')
@section('title', 'Push')
@section('content')

    <div class="container">
        <h1>PNs</h1>

        <p>
            Example:
        </p>
        <p>
            ⭐New!⭐Carol (Cate Blanchett) 🍅94%, The Big Short (Steve Carell) 🍅88%, Goosebumps (Jack Black) 🍅73%, The 5th Wave (Chloë Grace Moretz) No Rating
        </p>
        <form action="{{ URL::route('push.store') }}" method="post">
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" rows="3" id="message" name="message"></textarea>
            </div>
            @foreach($devices as $device)
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="devices[]" value="{{ $device->id }}" />
                    {{ $device->device_type }} - {{ $device->token }}
                </label>
            </div>
            @endforeach
            <button type="submit" class="btn btn-default">Submit</button>
        </form>
    </div>
@stop