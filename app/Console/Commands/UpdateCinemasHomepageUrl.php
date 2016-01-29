<?php

namespace MoviesOwl\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Cinemas\Cinema;
use Yangqi\Htmldom\Htmldom;

class UpdateCinemasHomepageUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owl:homepage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cinemas homepage url';

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
        Log::useFiles('php://stdout');
        $cinemas = Cinema::all();
        foreach ($cinemas as $cinema) {
            // search for cinema
            Log::info('Searching for cinema: ' . $cinema->location);
            $searchUrl = 'https://www.google.com.au/search?q=' . urlencode($cinema->location);
            $searchHtml = @file_get_contents($searchUrl);
            $searchDom = new Htmldom($searchHtml);
            $searchLink = $searchDom->find('#search a', 0);
            if ($searchLink) {
                $searchHref = $searchLink->href;
                parse_str($searchHref, $params);
                if (isset($params['/url?q'])) {
                    $homepageUrl = $params['/url?q'];
                    $cinema->homepage_url = $homepageUrl;
                    $cinema->save();
                    Log::info('Homepage url: ' . $homepageUrl);
                }
            }
        }
    }
}
