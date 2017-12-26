<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Image extends Model
{

	use Notifiable;
	
    protected $fillable = [
        'user_id', 'image_order','image_url'
    ];
}
