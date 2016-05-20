<?php
/**
 * Created by PhpStorm.
 * User: Kel
 * Date: 20/05/2016
 * Time: 5:23 PM
 */

namespace MoviesOwl\Cinema21;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Cinemas\Cinema;
use Yangqi\Htmldom\Htmldom;
use Carbon\Carbon;
class Cinema21Api
{
    protected $client;
    /**
     * Cinema21Api constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://m.21cineplex.com',
            // You can set any number of default request options.
            'timeout'  => 10,
        ]);
    }

    public function getCinemas()
    {
        $response = $this->client->request('POST', 'ios-service/service.php', [
            'form_params' => [
                'request_type' => 'list_theater_by_city',
                'city_id' => '10'
            ]
        ]);
        return json_decode((string)$response->getBody());
    }

    public function getMovies($cinema21Id) {
        $response = $this->client->request('POST', 'ios-service/service.php', [
            'form_params' => [
                'request_type' => 'schedule_by_cinema',
                'cinema_id' => $cinema21Id
            ]
        ]);
        return json_decode((string)$response->getBody());
    }

    public function getSeats($showing){
        $formParams = json_decode($showing->data, true);
        $formParams['request_type'] = "get_layout";
        $formParams['member_id'] = "WA149047141461358224053571205";
        $formParams['msisdn'] = "62818604913";
        $formParams['sid'] = "3e67844be24e8bc38d2c21cce19abdae";
        $response = $this->client->request('POST', 'ios-service/service.php', [
            'form_params' => $formParams
        ]);
        $seatData = json_decode((string)$response->getBody());
        $seats = [];
        foreach($seatData as $seatDataRow) {
            $row = [];
            // get the spaces
            $spaces = explode(";", $seatDataRow->arr_stairs);
            if(end($spaces) === '') {
                array_pop($spaces);
            }
            array_pop($spaces);
            foreach($seatDataRow->status as $index => $status) {
                if(in_array($index + 1, $spaces)) {
                    $row[] = "spacer";
                };
                $mapping = [
                    "1" => "available",
                    "2" => "available",
                    "5" => "taken",
                    "6" => "spacer"
                ];
                $row[] = $mapping[$status];
            }
            $seats[] = $row;
        }
//        $rows = array_map(function ($row) {
//            return $row->status;
//        }, $seatData);
//        $rowsOfSpaces = array_map(function ($row) {
//            $spaces = explode(";", $row->arr_stairs);
//            array_pop($spaces);
//            return $spaces;
//        }, $seatData);
//        $seats = array_map(function($row, $index) use ($rowsOfSpaces) {
//            $spaces = $rowsOfSpaces[$index];
//            return array_reduce($row, function($result, $status, $position) use ($spaces) {
//                if(in_array($position, $spaces)) {
//                    $result[] = "spacer";
//                }
//
//                $result[] = $mapping[$status];
//                return $result;
//            }, []);
//        }, $rows);
        return array_reverse($seats);

    }
}