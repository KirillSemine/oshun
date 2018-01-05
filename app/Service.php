<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Service extends Model
{

	use Notifiable;

	protected $table = 'services';

    protected $fillable = [
        'service_id', 'serviceName','styleName', 'style_id'
    ];
}