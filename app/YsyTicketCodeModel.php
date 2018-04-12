<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YsyTicketCodeModel extends Model
{
    //
    protected $table = 'ysy_ticket_code';
    protected $fillable = ['status', 'used_count', 'total_count', 'discount_price', 'hy_code'];
}
