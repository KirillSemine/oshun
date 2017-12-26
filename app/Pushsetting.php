<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Pushsetting extends Model
{
    protected $fillable = [
        'user_id', 'matched', 'liked','message','superliked'
    ];
    protected $casts = [ 'user_id' => 'integer', 'matched' => 'integer', 'liked' => 'integer', 'message' => 'integer', 'superliked' => 'integer'];
}
