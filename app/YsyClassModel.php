<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YsyClassModel extends Model
{
    //
    protected $table = 'ysy_class';
    protected $fillable=['clsname','status'];
}
