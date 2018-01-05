<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Imagelike extends Model
{
    use Notifiable;
	protected $table = 'imagelikes';

    protected $fillable = [
        'image_id', 'user_id','like_status'
    ];

    public function user(){
    	return $this->belongsTo(User::class, 'image_id');
    }
}