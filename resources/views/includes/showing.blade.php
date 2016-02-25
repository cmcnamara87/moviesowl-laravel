<?php
        $directTicket = (strlen($showing->tickets_url) && strpos($showing->tickets_url, "event") === false);
?>
@if($directTicket)
    <a data-showing-id="{{ $showing->id }}" class="time" target="_blank" href="{{ $showing->tickets_url }}" onclick="ga('send', 'event', 'button', 'buy_tickets');">
@else
    <a data-showing-id="{{ $showing->id }}" class="time" href="{{ url("showings/{$showing->id}") }}">
@endif
                            <span class="time__time">
                                {{ $showing->start_time->format('h:i A') }}
                            </span>
    @if($directTicket)
        Buy Tickets
    @endif

    @if($showing->screen_type != "standard")
        <span class="time__{{ str_replace(" ", "-", $showing->screen_type) }}">
            {{ $showing->screen_type }}
        </span>
    @endif
    @if($showing->showing_type != "standard" && $showing->showing_type != "")
        <span class="time__3d">
            {{ $showing->showing_type }}
        </span>
    @endif

    @if($showing->cinema_size && $showing->screen_type == 'standard')
    <span class="time__type">
        {{ $showing->cinema_size }} Cinema
    </span>
    @endif

    <div class="progress">
        <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
             style="width: {{ $showing->percent_full + 20 }}%;">
            {{ $showing->percent_full }}% Full
        </div>
    </div>
</a>