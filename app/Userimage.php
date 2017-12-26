<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Userimage extends Model
{
    use Notifiable;
	protected $primaryKey = 'id';
	
    protected $fillable = [
        'user_id', 'order','url'
    ];
}
