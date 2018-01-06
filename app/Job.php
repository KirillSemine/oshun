<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Job extends Model
{
    use Notifiable;
	protected $table = 'jobs';

    protected $fillable = [
        'id', 'from','to', 'start_time', 'timeline', 'styleName'
    ];

    public function user(){
    	return $this->belongsTo(User::class, 'image_id');
    }
}