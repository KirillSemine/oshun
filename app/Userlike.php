<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Userlike extends Model
{
    use Notifiable;
	protected $table = 'userlikes';

    protected $fillable = [
        'user_id', 'opponent_id','like_status','matched'
    ];

    public function user(){
    	return $this->belongsTo(User::class, 'opponent_id');
    }
}
