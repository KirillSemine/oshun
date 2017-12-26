<?php

namespace App;

use App\Userlike;
use App\Userimage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{


    protected $table = 'users';

    protected $primaryKey = 'id';

    public $userimages;
    
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'social_id','birthday','password', 'gender', 'app_token', 'device_token','interesting','avatar', 'job', 'phone', 'role_id', 'service', 'company'
    ];
    protected $casts = [ 'swipes' => 'integer', 'isPaid' => 'integer', 'userlikes' => 'integer', 'user_liked' => 'integer', 'user_disliked' => 'integer', 'botlikes' => 'integer'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function userimages()
    {
        return $this->hasMany(UserImage::class, 'user_id');
    }

    public function userlikescount()
    {
        return count($this->hasMany(Userlike::class, 'opponent_id')->where('userlikes.like_status','>',0)->get());
    }

    public function userlikedcount($ownerid)
    {
        return count($this->hasMany(Userlike::class, 'opponent_id')->where('user_id','=',$ownerid)->get());   
    }
    public function userlikedstatus($ownerid){
        return $this->hasMany(Userlike::class, 'user_id')->where('opponent_id','=',$ownerid)->first();
    }
}
