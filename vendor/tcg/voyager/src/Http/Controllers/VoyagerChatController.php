<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCG\Voyager\Facades\Voyager;
use App\Userimage;
use App\User;

class VoyagerChatController extends Controller
{
   public function index()
   {
    	$arrayCities = array();
    	

		$cities = User::select('city')->where("role_id",'<>',3)->whereRaw('city IS NOT NULL')->groupBy('city')->get();
		$users = User::where('role_id','<>','3')->get();
		foreach ($cities as $city) {
			$strUsers = "";
			foreach ($users as $user) {
				if($user->city == $city->city){
					$strUsers = $strUsers.$user->id.",";
				}
			}
			$arrayCities[$city->city] = $strUsers;
		}

		return view('voyager::chat.browse', compact('cities', 'arrayCities'));
    }
}