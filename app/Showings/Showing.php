<?php

namespace MoviesOwl\Showings;

use Carbon\Carbon;

/**
 * @property array seats
 */
class Showing extends \Eloquent {

    protected $_seats;

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ["movie_id", "cinema_id", "start_time",
        "type", "screen_type", "showing_type", "tickets_url", "event_session_id"];


    public function movie()
    {
        return $this->belongsTo('MoviesOwl\Movies\Movie');
    }
    public function cinema()
    {
        return $this->belongsTo('MoviesOwl\Cinemas\Cinema');
    }

    public function getSeatsAttribute($value)
    {
        return $this->_seats ?: ($this->_seats = json_decode($value));
    }
    public function setSeatsAttribute($seats)
    {
        $seatsCount = 0;
        foreach($seats as $row) {
            foreach($row as $seat) {
                if($seat != 'spacer') {
                    $seatsCount++;
                }
            }
        }
        $this->_seats = $seats;
        $this->attributes['seats'] = json_encode($this->_seats);
        $this->attributes['seats_count'] = $seatsCount;
        $this->attributes['cinema_size'] =  Showing::getScreenSizeFromSeats($seatsCount);
        $this->attributes['seats_updated_at'] = Carbon::now();
        $this->attributes['percent_full'] = 0;

        if($seatsCount > 0) {
            $takenSeatsCount = array_reduce($seats, function($carry, $seat) {
                $counts = array_count_values($seat);
                if(isset($counts['taken'])) {
                    return $counts['taken'] + $carry;
                }
                return $carry;;
            }, 0);
            $this->attributes['percent_full'] = $takenSeatsCount / $seatsCount * 100;
        }

    }


    public function scopeOnDate($query, Carbon $date)
    {
        $query->where('start_time', '>=', $date->toDateString());
        $query->where('start_time', '<', $date->addDay()->toDateString());
    }
    public function scopeAtCinema($query, $cinemaId)
    {
        $query->where('cinema_id', $cinemaId);
    }
    public function scopeStartingAfter($query, Carbon $startTime)
    {
        $query->where('start_time', '>=', $startTime);
    }

    public function getDates()
    {
        return array('created_at', 'updated_at', 'seats_updated_at');
    }

    public function getStartTimeAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value, $this->cinema->timezone);
    }

    /**
     * @param $seatsCount
     * @return string
     * @internal param $seatCount
     */
    public static function getScreenSizeFromSeats($seatsCount)
    {
        if(!isset($seatsCount) || $seatsCount == 0) {
            return 'unknown';
        }
        if ($seatsCount > 200) {
            return 'large';
        } else if ($seatsCount > 150) {
            return 'medium';
        } else {
            return 'small';
        }
        return 'standard';
    }

}