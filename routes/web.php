<?php

use App\Attendee;
use App\Arrivedattendee;
use App\Attendeeimage;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $hello = Array(
        'get details of user' => '/getdetails/{qr}',
        'set arrival time of user' => 'setarrival/{id}'
    );
    return $hello;
});

Route::get('/getdetails/{qr}', function ($qr){
    $attendee = Attendee::all()->where('qr_code',$qr)->first();
    if(count($attendee)){
        return $attendee;
    }else{
        //doesnt have match
        $error = Array(
            'error'=>'user not found',
            'search'=>$qr
            );
        return $error;
    }
});

Route::get('/getdetails1/{id}', function ($id){
    $attendee = Attendee::all()->where('id',$id)->first();
    if(count($attendee)){
        return $attendee;
    }else{
        //doesnt have match
        $error = Array(
            'error'=>'user not found',
            'search'=>$id
        );
        return $error;
    }
});

Route::get('/setarrival/{id}', function ($id) {

    $arrivedAttendee = Arrivedattendee::all()->where('attendee_id',$id)->first();
    if(count($arrivedAttendee)){
        $error = Array(
            'error'=>'user has already arrived'
        );
        return $error;
    }else{
        DB::insert('insert into arrivedattendees(attendee_id,created_at) values (?,?)', [$id,\Carbon\Carbon::now()]);
        DB::insert('insert into attendeeimages(attendee_id) values (?)',[$id]);
        $insertedAttendee = Arrivedattendee::all()->where('attendee_id',$id)->first();
        $response = Array(
            'status'=>'success'
        );
        return $response;
    }
});

Route::get('/getpendingtask', function (){
    $current = Attendeeimage::all();
    if (count($current)) {
        return $current->first();
    }else {
        return "";
    }
});

Route::get('/deletependingtask/{id}', function ($id){
    $current = Attendeeimage::all()->where('attendee_id',$id);
    if (count($current)) {
        DB::delete('delete from attendeeimages where attendee_id = ?', [$id]);

    }
});

Route::get('/raffle', function(){
    $attendees = Arrivedattendee::all()->sortBy('attendee_id');
    foreach ($attendees as $attendee){
        $person = Attendee::find($attendee->attendee_id);
        $fullname = $person->first_name." ".$person->last_name;
        echo $attendee->attendee_id.",".strtoupper($fullname)."<br>";


    }

});