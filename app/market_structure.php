<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class market_structure extends Model
{
    //
    protected $table = 'market_sructure';

    public function order()
    {
        return $this->hasMany(TicketOrder::class);
    }
}
