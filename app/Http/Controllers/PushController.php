<?php

namespace MoviesOwl\Http\Controllers;

use Illuminate\Http\Request;

use MoviesOwl\Device;
use MoviesOwl\Http\Requests;
use MoviesOwl\Http\Controllers\Controller;

class PushController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devices = Device::where('device_type', '=', 'iOS')->get();
        return view('push.index', compact('devices'));
    }

//
//Route::get('/push', function() {
//    $devices = \MoviesOwl\Device::where('device_type', '=', 'iOS')->get();
//    foreach($devices as $device) {
//        echo '<pre>';
//        print_r($device);
//        echo '</pre>';
//        \Davibennun\LaravelPushNotification\Facades\PushNotification::app('appNameIOS')
//            ->to($device->token)
//            ->send('Check out today\'s movies!');
//    }
//    echo 'Pushed';
//});

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $devices = Device::where('device_type', '=', 'iOS')->first();
        $devices = [$devices];
        foreach($devices as $device) {
            \Davibennun\LaravelPushNotification\Facades\PushNotification::app('appNameIOS')
                ->to($device->token)
                ->send($request->input('message'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
