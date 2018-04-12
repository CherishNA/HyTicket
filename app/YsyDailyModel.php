<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YsyDailyModel extends Model
{
    //
    protected $table = 'ysy_daily';
    protected $fillable=['dailyname','status','ddate'];

}
