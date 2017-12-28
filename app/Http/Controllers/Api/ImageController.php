<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Userimage;
use App\Http\Requests;
use App\Http\Requests\ImageuploadRequest;
use App\Http\Requests\ImagelistRequest;
use Tymon\JWTAuth\JWTAuth;
use JWTAuthException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;
use Intervention\Image\Facades\Image;
use Intervention\Image\Constraint;
use App\Userlike;
      
class ImageController extends Controller
{

  private $user;
  private $jwtauth;
  private $userimage;
  private $filesystem;

  public function __construct(User $user, Userimage $userimage, JWTAuth $jwtauth)
  {
    $this->user = $user;
    $this->jwtauth = $jwtauth;
    $this->userimage = $userimage;

    $this->filesystem = config('voyager.storage.disk');
  }

  Public function get_images(ImagelistRequest $request){

  	$token = $request->get('token');
    $this->user = $this->jwtauth->toUser($token);
  	$images = Userimage::where('user_id', '=', $this->user->id)->get();

  	return response()->json([
  	  'status' => 'success',
      'image' => $images
      ]);
  }

  Public function deleteImage(Request $request, $id){

      try {
        $token = $request->get('token');
        $this->user = $this->jwtauth->toUser($token);

        $image = Userimage::where('user_id', '=', $this->user->id)->where('order', $id)->get();

        Storage::disk($this->filesystem)->delete($image[0]->url);

        Userimage::where('user_id', '=', $this->user->id)->where('order', $id)->delete();
        

        $images = Userimage::where('user_id', '=', $this->user->id)->get();
        if(count($images)>0){
            $this->user->avatar = $images[0]->url;
            $this->user->save();
        }

        return response()->json([
          'status' => 'success',
          'result' => 'deleted image successfully'
          ]);

       }catch (\Exception $e) {
         return response()->json([
           'status' => 'error',
           'error' => 'failed delete image'], 500);
     }

  }

  public function update_proilfeImage(Request $request)
  {
    $fullFilename = null;
    $resizeWidth = 1800;
    $resizeHeight = null;    


    $user_id = $request->get('user_id');
    $file = $request->file('image');

    $extension = $file->getClientOriginalExtension();

    $filename = Str::random(20);

    $fullPath = 'users/'.$user_id.'/'.$filename.'.'.$file->getClientOriginalExtension();
    $ext = $file->guessClientExtension();

    $image = Image::make($file)->resize($resizeWidth, $resizeHeight, function(Constraint $constraint){
      $constraint->aspectRatio();
      $constraint->upsize();
    })->encode($file->getClientOriginalExtension(), 75);

    //move uploaded file from temp to uploads directory
    if(Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public')){
      $status = 'Image successfully uploaded!';
      $fullFilename = $fullPath;
    } else {
      $status = 'Upload Fail: Unknown error occurred!';
      return response()->json([
        'result' => 'error',
        'error' => $status
      ]); 
    }

    $currentuser = User::where('id', '=', $user_id)->first();
    $currentuser->avatar = $fullPath;
    $currentuser->save();

    return response([
      'result' => 'success',
      'user' => $currentuser
    ]);

  }

  Public function upload_profile(ImageuploadRequest $request)
  {

  	$fullFilename = null;
    $resizeWidth = 1800;

    $resizeHeight = null;
   

    $token = $request->get('token');
    $order = $request->get('order');
    $file = $request->file('image');

    $extension = $file->getClientOriginalExtension();
    $this->user = $this->jwtauth->toUser($token);
    $filename = Str::random(20);

    $fullPath = 'users/'.$this->user->id.'/'.$filename.'.'.$file->getClientOriginalExtension();
  	$ext = $file->guessClientExtension();

    if (in_array($ext, ['jpeg', 'jpg', 'png', 'gif'])) {
        $image = Image::make($file)-> resize($resizeWidth, $resizeHeight, function (Constraint $constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->encode($file->getClientOriginalExtension(), 75);

        // move uploaded file from temp to uploads directory
        if (Storage::disk(config('voyager.storage.disk'))->put($fullPath, (string) $image, 'public')) {
            $status = 'Image successfully uploaded!';
            $fullFilename = $fullPath;
        } else {
            $status = 'Upload Fail: Unknown error occurred!';
            return response()->json([
		  	  'result' => 'error',
		      'error' => $status
		      ]);
        }
    } else {
        $status = 'Upload Fail: Unsupported file format or It is too large to upload!'; 
        return response()->json([
		  	  'result' => 'error',
		      'error' => $status
		      ]);
    }

    $newImage = Userimage::where('user_id', '=', $this->user->id)->
    						   where('order', '=', $order)->first();
    if (is_null($newImage)) {
    	$newImage = $this->userimage->create([
	      'user_id' => $this->user->id,
	      'order' => $order,
	      'url' => $fullPath
	    ]);
    }else{
    	$newImage->url = $fullPath;
    	$newImage->save();

    }

    $images = Userimage::where('user_id', '=', $this->user->id)->get();
    if(count($images)>0){
        $this->user->avatar = $images[0]->url;   
        $this->user->save();
    }
    //make 5 random botlikes when user create first image


  	
    $newImage->url = Voyager::image($fullPath);
  	return response()->json([
  	  'result' => 'success',
      'image' => $newImage
      ]);
  }
}
