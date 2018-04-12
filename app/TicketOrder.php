<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketOrder extends Model
{
    //
    protected $table = 'order';
    protected $fillable = ['order_type', 'order_mobile', 'sign_status', 'seatno', 'area', 'recommend_id', 'username', 'openid', 'ucid', 'pay_time', 'pay_status', 'order_info', 'order_price'];

    public function market()
    {
        return $this->belongsTo(market_structure::class,'recommend_id','cid');
    }
}
