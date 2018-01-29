<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DateTime;
use Carbon\Carbon;

use App\User;
use App\Userimage;
use App\Userlike;
use App\Imagelike;
use App\Service;
use App\Job;
use App\Botmessage;
use App\Pushsetting;
use DB;

use App\Http\Requests;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LocationRequest;
use App\Http\Requests\UserlikeRequest;

use TCG\Voyager\Facades\Voyager;

use Tymon\JWTAuth\JWTAuth;
use Davibennun\LaravelPushNotification\PushNotification;
use JWTAuthException;

class JobController extends Controller
{
  private $user;
  private $jwtauth;
  private $userlike;
  public function __construct(User $user, JWTAuth $jwtauth)
  {
    $this->user = $user;
    $this->jwtauth = $jwtauth;
  }
  
  public function jobrequest(Request $request){
    $from = $request->get('user_id');
    $to   = $request->get('opponent_id');
    $start_time = $request->get('start_time');
    $timeline   = $request->get('timeline');
    $stylename  = $request->get('stylename');

    $job = Job::create([
      'from' => $from, 
      'to'   => $to,
      'start_time'  => $start_time,
      'timeline'    => $timeline, 
      'styleName'   => $stylename
    ]);

    $user = User::where('id', '=', $from)->first();
    $oppoentuser = User::where('id', '=', $to)->first();

    PushNotification::app('Oshun')
                      ->to($oppoentuser->device_token)
                      ->send($user->name.' has awarded you for.');

    return response()->json([
      'status' => 'success',
      'result' => $job
    ]);
    
  }

  public function jobedit(Request $request){
    $job_id = $request->get('job_id');
    $from = $request->get('user_id');
    $to   = $request->get('opponent_id');
    $start_time = $request->get('start_time');
    $timeline   = $request->get('timeline');
    $stylename  = $request->get('stylename');


    $job = Job::where('id', '=', $job_id)->first();
    $job->start_time = $start_time;
    $job->timeline = $timeline;
    $job->styleName = $stylename;
    $job->save();

    return response()->json([
      'status' => 'success',
      'result' => $job
    ]);
  }

  public function jobdelete(Request $request){
    $job_id = $request->get('job_id');
    $job = Job::where('id', '=', $job_id)->first();

    if (!is_null($job)) {
      Job::where('id', '=', $job_id)->delete();

      return response()->json([
      'status' => 'success',
      'result' => 'job deleted'
      ]);
    } else {
      return response()->json([
      'status' => 'failed',
      'result' => 'no job'
      ]);
    }

  }

  public function getAllJob(Request $request){
    $user_id = $request->get('user_id');
    $role_id = $request->get('role_id');

    if ($role_id == 2) {
      $jobs = Job::where('from', '=', $user_id)->get();
    } else {
      $jobs = Job::where('to', '=', $user_id)->get();
    }

    return response()->json([
      'status' => 'success',
      'result' => $jobs
    ]);

  }

  public function jobAccept(Request $request){
    $job_id = $request->get('job_id');
    $status = $request->get('status');

    $job = Job::where('id', '=', $job_id)->first();
    $job->accept = 1;
    $job->save();
    
    $user = User::where('id', '=', $job->from)->first();
    $oppoentuser = User::where('id', '=', $job->to)->first();


    PushNotification::app('Oshun')
                      ->to($user->device_token)
                      ->send($oppoentuser->name.' accepted your offer.');

    return response()->json([
      'status' => 'success',
      'result' => $job
    ]);
  }

  public function jobEnd(Request $request){
    $job_id = $request->get('job_id');

    $job = Job::where('id', '=', $job_id)->first();
    $job->end = 1;
    $job->save();

    return response()->json([
      'status' => 'success',
      'result' => $job
    ]);
  }

  function distance($lat1, $lon1, $lat2, $lon2, $unit) {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
  }


  }