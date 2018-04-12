<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YsyOrderModel extends Model
{
    //
    protected $table = 'ysy_order';
    protected $fillable = ['name', 'order_mobile', 'sex', 'ucid', 'job', 'area',
        'recommend', 'class_id', 'subject_id', 'daily_id', 'pay_time'
        , 'pay_status', 'order_info', 'order_price', 'pro_id'];
}
