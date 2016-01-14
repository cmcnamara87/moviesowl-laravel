@extends('layouts.default')
@section('title', 'Push')
@section('content')

    <div class="container">
        <h1>PNs</h1>

        <form action="{{ URL::route('push.store') }}" method="post">
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" rows="3" id="message" name="message"></textarea>
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        </form>

        <h2>Devices</h2>
        <ul>
            @foreach($devices as $device)
                <li>
                    {{ $device->device_type }} - {{ $device->token }}
                </li>
            @endforeach
        </ul>
    </div>
@stop