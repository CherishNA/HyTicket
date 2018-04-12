<?php

namespace LaraMall\Weixin\Models;

use App\TicketOrder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hash;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = "wx_user";
    protected $fillable = [
        'nickname',
        'openid',
        'avatar',
    ];

}