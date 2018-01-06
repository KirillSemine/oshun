<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Userrelation extends Model
{
    use Notifiable;
	protected $table = 'userrelations';

    protected $fillable = [
        'user_id', 'opponent_id','favourite_status','matched'
    ];

    public function user(){
    	return $this->belongsTo(User::class, 'opponent_id');
    }
}