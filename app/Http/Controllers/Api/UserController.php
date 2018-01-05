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

class UserController extends Controller
{
  private $user;
  private $jwtauth;
  private $userlike;
  public function __construct(User $user, JWTAuth $jwtauth)
  {
    $this->user = $user;
    $this->jwtauth = $jwtauth;
  }

  public function register(Request $request)
  {

      $name = $request->get('name');
      $email = $request->get('email');
      if(is_null($request->get('social_id'))){
        $password = bcrypt($request->get('password'));
        $social_id = "";
      }else{
        $password = bcrypt($request->get('social_id'));
        $social_id = $request->get('social_id');
      }


        
      $role_id = $request->get('role');
      $service = "";
      $company = "";
      if($role_id == 3){
        $service = $request->get('service');
        $company = $request->get('company');
      }

      $birthday = $request->get('birthday');
      $gender = 0;
      $phone = $request->get('phone');
      // $job = $request->get('job');
      $latitude = $request->get('latitude');
      $longitude = $request->get('longitude');
      // $interesting = $request->get('interesting');
      // $device_token = $request->get('device_token');

      $newUser = User::where('email', '=', $email)->first();

      if (is_null($newUser)) {

        $newUser = $this->user->create([
          'name'      => $name,
          'email'     => $email,
          'phone'     => $phone,
          'password'  => $password,
          'social_id' => $social_id,
          'birthday'  => $birthday,
          'gender'    => $gender,
          'role_id'   => $role_id,
          'service'   => $service,
          'company'   => $company,
          // 'job'       => $job,
          'latitude'  => $latitude,
          'longitude' => $longitude,
          // 'interesting' => $interesting,
          // 'device_token' => $device_token
          
        ]);

         // $pushsetting = Pushsetting::create([
         //          'user_id'     => $newUser->id,
         //          'matched' => 1,
         //          'liked' => 1,
         //          'message' => 1,
         //          'superliked' => 1
         //    ]);




        
      }else{
        return response()->json([
        'status' => 'failed',  
        'msg' => "already exist"
      ]);
        // $newUser->name      = $name;
        // $newUser->email     = $email;
        // $newUser->password  = $password;
        // $newUser->social_id = $social_id;
        // $newUser->birthday  = $birthday;
        // $newUser->gender    = $gender;
        // // $newUser->job       = $job;
        // $newUser->latitude  = $latitude;
        // $newUser->longitude = $longitude;
        // $newUser->device_token = $device_token;
        // if($newUser->isPaid == 1){
        //   $to = Carbon::createFromFormat('Y-m-d H:s:i', $newUser->purchasedate);
        //   $mytime = Carbon::now();
        //   $lengthOfAd = $to->diffInDays($mytime);
        //   if($lengthOfAd>30){
        //     $newUser->isPaid = 0;
        //   }
        // }
  
        
        // // $newUser->interesting = $interesting;
        // $newUser->save();

      }



      if (!$newUser) {

        return response()->json(['failed_to_create_new_user'], 500);
      }
      // $newUser->app_token = $this->jwtauth->fromUser($newUser);
      // $newUser->save();

      $images = Userimage::where('user_id', '=', $newUser->id)->get();

      // $botlikes = Userlike::join('users', function($join)
      // {
      //     $join->on('users.id', '=', 'userlikes.user_id');

      // })->where('opponent_id', '=', $newUser->id)->where('users.role_id','=','3')->get();

      // $newUser->botlikes = count($botlikes);
      // $todayswipes = Userlike::where('user_id' , '=', $newUser->id)->whereRaw('DATE(created_at) = CURDATE()')->get();
      // $newUser->swipes = count($todayswipes);

      // $newUser->save();


      // $pushsetting = Pushsetting::where('user_id', '=', $newUser->id)->first();


      // $newUser->setAttribute('pushsetting',$pushsetting);


      // $oldusers = User::whereRaw('updated_at <= DATE_SUB(CURDATE(), INTERVAL 1 DAY)')->where('gender','=','1')->where('role_id','<>','3')->get();
      // foreach ($oldusers as $olduser) {
      //   PushNotification::app('Datelux')
      //           ->to($olduser->device_token)
      //           ->send("Don't miss Mr. Right! 20 number of new mail profiles have been added since your last visit");
      // }
      

      //TODO: implement JWT
      return response()->json([
        'status' => 'success',  
        'user' => $newUser,
        'images' => $images
    	]);
    // }
    // catch (\Exception $e) {
    //     return response()->json([
    //       'status' => 'error',
    //       'error' => 'failed_to_create_new_user'], 500);
    // }
  }

  public function updatelocation(LocationRequest $request){

    $token = $request->get('token');
    $this->user = $this->jwtauth->toUser($token);

    $this->user->latitude = $request->get('latitude');
    $this->user->longitude = $request->get('longitude');
    $this->user->city = $request->get('city');
    $this->user->save();


    return response()->json([
      'status' => 'success',
      'user' => $this->user
      ]);

  }
  public function updateUser(Request $request){

    $token    = $request->get('token');
    $bio      = $request->get('bio');
    $birthday = $request->get('birthday');
    $gender   = $request->get('gender');
    $job      = $request->get('job');
    $interesting = $request->get('interesting');
    $status   = $request->get('status');
    

    $this->user = $this->jwtauth->toUser($token);

    $this->user->bio      = $request->get('bio');
    $this->user->gender   = $request->get('gender');
    $this->user->birthday = $request->get('birthday');
    $this->user->job      = $request->get('job');
    $this->user->interesting = $request->get('interesting');
    $this->user->status   = $status;
    $this->user->save();


    return response()->json([
      'status' => 'success',
      'user' => $this->user
      ]);

  }

  public function updatePush(Request $request){

    $token    = $request->get('token');
    $matched      = $request->get('matched');
    $liked = $request->get('liked');
    $message   = $request->get('message');
    $superliked      = $request->get('superliked');
    

    $this->user = $this->jwtauth->toUser($token);

    $pushsetting = Pushsetting::where('user_id', '=',$this->user->id)->first();
    $pushsetting->matched = $matched;
    $pushsetting->liked = $liked;
    $pushsetting->message = $message;
    $pushsetting->superliked = $superliked;

    $pushsetting->save();

    $this->user->setAttribute('pushsetting',$pushsetting);

    return response()->json([
      'status' => 'success',
      'user' => $this->user
      ]);

  }

  public function login(Request $request)
	{
	  // get user credentials: email, password
    
	  $credentials = $request->only('email');
    $credentials['password'] = $request->get('password');
	  $token = null;
	  try {
	    $token = $this->jwtauth->attempt($credentials);    
	    if (!$token) {
	      return response()->json(['status' => 'error',
                                  'error' => 'Invalid Email or Password'
                                  ], 422);
	    }
	  } catch (JWTAuthException $e) {
	    return response()->json(['status' => 'error',
                                  'error' => 'failed_to_create_token'
                                  ], 500);
	  }
    $this->user = $this->jwtauth->toUser($token);
    $this->user->app_token = $token;
    $this->user->save();

    $images = Userimage::where('user_id', '=', $this->user->id)->get();
    // $this->user = $user->update(['app_token' => $token]);

    $currentuser = $this->user;

    $currentuser->avatar = Voyager::image($this->user->avatar);

    return response()->json([
        'status' => 'success',  
        'user' => $currentuser,
        'images' => $images
      ]);
	}

  public function testAPN(Request $request){
    $token        = $request->get('token');
    $this->user = $this->jwtauth->toUser($token);

    PushNotification::app('Datelux')
                ->to($this->user->device_token)
                ->send('You and '.$this->user->name.' are mached each other!');
  }

  public function botmatch(Request $request){

    $token = $request->get('token');
    $this->user = $this->jwtauth->toUser($token);

    $oppoentuser = User::leftJoin('userlikes', function($join)
    {
        $join->on('users.id', '=', 'userlikes.user_id');

    })->whereRaw('(userlikes.opponent_id <> '.$this->user->id .' OR userlikes.opponent_id IS NULL)')->where('role_id','=','3')->groupBy('main_user_id')->orderByRaw('RAND()')->take(1)->get(['users.id as main_user_id', 'users.*', 'userlikes.*'])->first();

    if(is_null($oppoentuser)){
        $oppoentuser = User::leftJoin('userlikes', function($join)
        {
            $join->on('users.id', '=', 'userlikes.user_id');

        })->whereRaw('(userlikes.opponent_id <> '.$this->user->id .' OR userlikes.opponent_id IS NULL)')->where('role_id','=','3')->orderByRaw('RAND()')->take(1)->get(['users.id as main_user_id', 'users.*', 'userlikes.*'])->first();
    }
    
    if(!is_null($oppoentuser)){

      $newUserlike = Userlike::create([
          'user_id'     => $oppoentuser->main_user_id,
          'opponent_id' => $this->user->id,
          'like_status' => 1,
          'matched'     => 0
          ]);
      $this->user->user_liked = $this->user->user_liked + 1;
      $this->user->save();


      $opponentlike = Userlike::where('opponent_id', '=', $oppoentuser->main_user_id)->where('user_id', '=', $this->user->id)->where('like_status', '>', 0)->first();

      $matched = 0;

      if(!is_null($opponentlike)){
        
        $newUserlike->matched = 1;
        $newUserlike->save();

        $matched = 1;
        $opponentlike->matched = 1;
        $opponentlike->save();
        $pushsetting = Pushsetting::where('user_id', '=', $this->user->id)->first();
        if($pushsetting->matched == 1){
          PushNotification::app('Datelux')
                      ->to($this->user->device_token)
                      ->send('You and '.$oppoentuser->name.' are mached each other!');
        }

        $userlike = Userlike::where('user_id', '=', $this->user->id)->where('opponent_id', '=', $oppoentuser->main_user_id)->first();
        if(!is_null($userlike)){
          $userlike->linked = 1;
          $userlike->save();
        }

        $opponentlike = Userlike::where('opponent_id', '=', $this->user->id)->where('user_id', '=', $oppoentuser->main_user_id)->first();
        if(!is_null($opponentlike)){
          $opponentlike->linked = 1;
          $opponentlike->save();
        }

        $user_id = $oppoentuser->main_user_id;
        $name = $oppoentuser->name;
        $opponent_id = $this->user->id;
        $opponent_name = $this->user->name;


        $rand_botmessage = Botmessage::orderByRaw('RAND()')->first();
        $message = $rand_botmessage->content;

        $this->user->botmessaged = 1;
        $this->user->save();


        $oppoentuser = User::where('id','=',$user_id)->first();

        $dt = new DateTime;
        $oppoentuser->updated_at = $dt->format('Y-m-d H:i:s');
        $oppoentuser->save();
        

        return view('chat',compact('user_id', 'name', 'opponent_id', 'opponent_name', 'message'));
        
      }else{
        // $user_id = $oppoentuser->main_user_id;
        // $name = $oppoentuser->name;
        // $opponent_id = $this->user->id;
        // $opponent_name = $this->user->name;


        // $rand_botmessage = Botmessage::orderByRaw('RAND()')->first();
        // $message = $rand_botmessage->content;
        

        // return view('chat',compact('user_id', 'name', 'opponent_id', 'opponent_name', 'message'));
      }
    } 
 }

  public function sendchat(Request $request){
    
    $token        = $request->get('token');
    $opponent_id  = $request->get('opponent_id');
    $like_status  = $request->get('like_status');    
    $this->user = $this->jwtauth->toUser($token);
    $oppoentuser = User::where('id','=',$opponent_id)->first();

    $userlike = Userlike::where('user_id', '=', $this->user->id)->where('opponent_id', '=', $opponent_id)->first();
    if(!is_null($userlike)){
      $userlike->linked = 1;
      $userlike->save();
    }

    $opponentlike = Userlike::where('opponent_id', '=', $this->user->id)->where('user_id', '=', $opponent_id)->first();
    if(!is_null($opponentlike)){
      $opponentlike->linked = 1;
      $opponentlike->save();
    }


    $user_id = $opponent_id;
    $name = $oppoentuser->name;
    $opponent_id = $this->user->id;
    $opponent_name = $this->user->name;


    $rand_botmessage = Botmessage::orderByRaw('RAND()')->first();
    $message = $rand_botmessage->content;
    

    return view('chat',compact('user_id', 'name', 'opponent_id', 'opponent_name', 'message'));
  }

  public function imagelike(Request $request){
    $image_id   = $request->get('image_id');
    $user_id    = $request->get('user_id');

    $imageliked = Imagelike::where('image_id', '=', $image_id)->where('user_id', '=', $user_id)->first();
    if (is_null($imageliked)) {
      $imageliked = Imagelike::create([
        'image_id' => $image_id,
        'user_id'  => $user_id,
        'like_status' => 1
      ]);
    }

    $likedimage = Userimage::where('id', '=', $image_id)->first();
    $likedimage->likes = $likedimage->likes + 1;
    $likedimage->save();

    return response()->json([
       'status' => 'success',
       'result' => $imageliked
       ]); 

  }

  public function imageliked(Request $request){
    $image_id   = $request->get('image_id');
    $user_id    = $request->get('user_id');

    $imageliked = Imagelike::where('image_id', '=', $image_id)->where('user_id', '=', $user_id)->first();

    if (!is_null($imageliked)){
      return response()->json([
       'status' => 'success',
       'liked' => 1
       ]);
    } else {
      return response()->json([
       'status' => 'success',
       'liked' => 0
       ]);
    }
  }

  public function userlike(UserlikeRequest $request){

    $token        = $request->get('token');
    $opponent_id  = $request->get('opponent_id');
    $like_status  = $request->get('like_status');

    $this->user = $this->jwtauth->toUser($token);
    $oppoentuser = User::where('id','=',$opponent_id)->first();
    if(!is_null($oppoentuser)){
      if($like_status > 0)
        $oppoentuser->user_liked = $oppoentuser->user_liked + 1;
      else
        $oppoentuser->user_disliked = $oppoentuser->user_disliked + 1;
      $oppoentuser->save();
    }
    $this->user->userlikes = $this->user->userlikes + 1;
    $this->user->save();


    $opponentlike = Userlike::where('opponent_id', '=', $this->user->id)->where('user_id', '=', $opponent_id)->where('like_status', '>', 0)->first();

    $matched = 0;

    if(!is_null($opponentlike) && $like_status > 0){

      $matched = 1;
      $opponentlike->matched = 1;
      $opponentlike->save();
      if($oppoentuser->role_id != 3){

        $pushsetting = Pushsetting::where('user_id', '=', $oppoentuser->id)->first();
        if($pushsetting->matched == 1){


          PushNotification::app('Datelux')
                    ->to($oppoentuser->device_token)
                    ->send('You and '.$this->user->name.' are matched each other!');
        }
      }

    }else{

      if($oppoentuser->role_id != 3){
        $pushsetting = Pushsetting::where('user_id', '=', $oppoentuser->id)->first();
          if($pushsetting->liked == 1){
            if($like_status == 1){
              PushNotification::app('Datelux')
                          ->to($oppoentuser->device_token)
                          ->send($this->user->name.' liked you!');
            }

          }
          if($pushsetting->superliked == 1){
              if($like_status == 2){
                PushNotification::app('Datelux')
                            ->to($oppoentuser->device_token)
                            ->send($this->user->name.' super liked you!!!');
              }
          }
        }
    }


    $newUserlike = Userlike::create([
          'user_id'     => $this->user->id,
          'opponent_id' => $opponent_id,
          'like_status' => $like_status,
          'matched'       => $matched
    ]);

    // $this->user->swipes = $this->user->swipes + 1;
    $this->user->save();

    $user_id = $opponent_id;
    $name = $oppoentuser->name;
    $opponent_id = $this->user->id;
    $opponent_name = $this->user->name;


      return response()->json([
       'status' => 'success',
       'result' => $newUserlike
       ]); 
   // 
   // 
   // 
   // 
  } 

  public function setPaid(Request $request){
    $token        = $request->get('token');
    $isPaid       = $request->get('paid');

    $this->user = $this->jwtauth->toUser($token);
    $this->user->isPaid = $isPaid;
    $dt = new DateTime;
    $this->user->purchasedate = $dt->format('Y-m-d H:i:s');
    $this->user->save();

    return response()->json([
      'status' => 'success'
      ]); 
  }

  public function setStatus(Request $request){
    $token        = $request->get('token');
    $status       = $request->get('status');

    $this->user = $this->jwtauth->toUser($token);
    $this->user->status = $status;
    $this->user->save();

    return response()->json([
      'status' => 'success'
      ]); 
  }

  public function matchedusers(Request $request){

    $token = $request->get('token');
    $this->user = $this->jwtauth->toUser($token);
    $result_newmatched = array();
    $result_matched = array();


    $userlikes = Userlike::where('user_id', '=', $this->user->id)->where('matched', '=', 1)->with('User')->get();
    foreach ($userlikes as $like) {

      $user = $like->user;
      $user->setAttribute('likecount',$user->userlikescount());
        $userlike = $user->userlikedstatus($this->user->id);
        if($userlike){
          $user->setAttribute('liked',(int)$userlike->like_status);
        }else{
          $user->setAttribute('liked',0);
        }
        if($like->linked == 0 && $like->user->status == 1){
          array_push($result_newmatched, ['user' => $user, 
                               'images'=> $user->userimages()->get()]);  
        }else if($like->user->status == 1){
          array_push($result_matched, ['user' => $user, 
                               'images'=> $user->userimages()->get()]);  
        }
    }

    return response()->json([
      'status' => 'success',
      'result_new' => $result_newmatched,
      'result_linked' => $result_matched,
      ]);  
  }

  public function userlist(Request $request){
    $token      = $request->get('token');
    $latitude   = $request->get('latitude');
    $longitude  = $request->get('longitude');
    $gender     = $request->get('gender');
    $maxage     = $request->get('maxage');
    $minage     = $request->get('minage');
    $distance   = $request->get('distance');
    $page       = intval($request->get('page'));
    $count      = intval($request->get('count'));

    $this->user = $this->jwtauth->toUser($token);

     $haversine = "(6371 * acos(cos(radians($latitude)) 
                     * cos(radians(`latitude`)) 
                     * cos(radians(`longitude`) 
                     - radians($longitude)) 
                     + sin(radians($latitude)) 
                     * sin(radians(`latitude`))))";

    $offset = $count*$page;


    if($gender != 2)
      $users = User::select('users.*')->selectRaw("{$haversine} AS distance")->leftJoin(DB::raw("
        (select * from userlikes where user_id = ".$this->user->id.") as `userlikes` "), function($join)
      {
          $join->on('users.id', '=', 'userlikes.opponent_id');

      })->whereRaw('(userlikes.user_id <> '.$this->user->id.' OR userlikes.user_id IS NULL)')->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE())  Between '.$minage.' And '.$maxage)->where('gender','=',$gender)->groupBy('id')->havingRaw('(distance < '.$distance.' or role_id = 3)')->where('status','=',1)->orderBy('user_liked', 'desc')->skip($offset)->take($count)->get();
    else
      $users = User::select('users.*')->selectRaw("{$haversine} AS distance")->leftJoin(DB::raw("
        (select * from userlikes where user_id = ".$this->user->id.") as `userlikes` "), function($join)
      {
          $join->on('users.id', '=', 'userlikes.opponent_id');

      })->whereRaw('(userlikes.user_id <> '.$this->user->id.' OR userlikes.user_id IS NULL)')->whereRaw('TIMESTAMPDIFF(YEAR, birthday, CURDATE())  Between '.$minage.' And '.$maxage)->groupBy('id')->havingRaw('(distance < '.$distance.' or role_id = 3)')->where('status','=',1)->orderBy('user_liked', 'desc')->skip($offset)->take($count)->get();

    $result = array();


    $totalcount = count($users);

    foreach ($users as $user){
      // $userdistance = $this->distance($latitude, $longitude, $user->latitude, $user->longitude, 'M');
      // if($userdistance < $distance || $user->role_id == 3){
        // $user->setAttribute('images',$user->userimages()->get());
       $user->setAttribute('viewed',0);
          
       array_push($result, ['user'=> $user, 
                             'images'=> $user->userimages()->get()]);    

        // $user->setAttribute('likecount',$user->userlikescount());
        // $userlike = $user->userlikedstatus($this->user->id);
        // if($userlike){
        //   $user->setAttribute('liked',(int)$userlike->like_status);
        // }else{
        //   $user->setAttribute('liked',0);
        // }
        // array_push($result, ['user'=> $user, 
        //                      'images'=> $user->userimages()->get()]);  
    }

    return response()->json([
      'status' => 'success',
      'result' => $result
      ]);  
  }

  function searchUserlist(Request $request){
    $role_id = $request->get('role_id');
    $limit = $request->get('limit');
    $latitude = $request->get('latitude');
    $longitude = $request->get('longitude');
    $random = $request->get('random');

    $haversine = "(6371 * acos(cos(radians($latitude)) 
                     * cos(radians(`latitude`)) 
                     * cos(radians(`longitude`) 
                     - radians($longitude)) 
                     + sin(radians($latitude)) 
                     * sin(radians(`latitude`))))";


    if ($random == 1) {
      $users  = User::select('users.*')->selectRaw("{$haversine} AS distance")->havingRaw('distance < 35*1.609344')->where('role_id', '=', $role_id)->orderByRaw('RAND()')->take($limit)->get();
    } else {
      $users  = User::select('users.*')->selectRaw("{$haversine} AS distance")->havingRaw('distance < 35*1.609344')->where('role_id', '=', $role_id)->take($limit)->get();
    }
    
    // return full image url
    foreach ($users as $tempuser){
      $tempuser->avatar = Voyager::image($tempuser->avatar);
    }

    return response()->json([
      'status' => 'success',
      'result' => $users
    ]);
  }

  function searchstylelist(Request $request){
    $latitude = $request->get('latitude');
    $longitude = $request->get('longitude');
    $style = $request->get('style');

    $haversine = "(6371 * acos(cos(radians($latitude)) 
                     * cos(radians(`latitude`)) 
                     * cos(radians(`longitude`) 
                     - radians($longitude)) 
                     + sin(radians($latitude)) 
                     * sin(radians(`latitude`))))";

    $service = Service::where('styleName', '=', $style)->first();


    if (is_null($service)) {
      return response()->json([
      'status' => 'failed',
      'result' => 'style error'
    ]);
    }

    $users = User::select('users.*')->selectRaw("{$haversine} AS distance")->havingRaw('distance < 35*1.609344')->whereRaw("service like '%".$service->service_id."%'")->get();

    return response()->json([
      'status' => 'success',
      'result' => $users
    ]);
  }

  function givefeedback(Request $request){
    $user_id = $request->get('user_id');
    $rating = $request->get('rating');
    $user = User::where('id', '=', $user_id)->first();
    $currentrating = $user->rating;
    $ratingcount = $user->rating_count;
    $temp = $currentrating*$ratingcount+$rating;
    $ratingcount = $ratingcount + 1;
    $user->rating = $temp/$ratingcount;
    $user->rating_count = $ratingcount;
    $user->save();

    return response()->json([
      'status' => 'success',
      'rating' => $user->rating
    ]);
  }

  function userlinked(Request $request){

    $token    = $request->get('token');
    $opponent_id = $request->get('opponent_id');

    $this->user = $this->jwtauth->toUser($token);

    $userlike = Userlike::where('user_id', '=', $this->user->id)->where('opponent_id', '=', $opponent_id)->first();
    if(!is_null($userlike)){
      $userlike->linked = 1;
      $userlike->save();
    }

    $opponentlike = Userlike::where('opponent_id', '=', $this->user->id)->where('user_id', '=', $opponent_id)->first();
    if(!is_null($opponentlike)){
      $opponentlike->linked = 1;
      $opponentlike->save();
    }
    return response()->json([
      'status' => 'success'
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