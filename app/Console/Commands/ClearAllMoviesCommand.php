<?php

namespace MoviesOwl\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MoviesOwl\Showings\Showing;

class ClearAllMoviesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:clear-showings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove showings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->info('1. Today');
        $this->info('2. Tomorrow');
        $this->info('3. Both');
        $dayToClear = $this->ask('Clear', false);

        if(!$dayToClear) {
            $this->info('Please specify a day');
            return;
        }
        if($dayToClear == '1') {
            $startingAfter = Carbon::today();
            $endOfDay = $startingAfter->copy()->endOfDay();
        }
        if($dayToClear == '2') {
            $startingAfter = Carbon::tomorrow();
            $endOfDay = $startingAfter->copy()->endOfDay();
        }
        if($dayToClear == '3') {
            $startingAfter = Carbon::today();
            $endOfDay = $startingAfter->copy()->tomorrow()->endOfDay();
        }

        $showings = Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->get();

        foreach($showings as $showing) {
            $showing->delete();
        }

        // supposed to only apply to a single connection and reset it's self
        // but I like to explicitly undo what I've done for clarity
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
