<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YsyProject extends Model
{
    //
    protected $table = 'ysy_project';
    protected $fillable = ['project_name', 'project_price','status'];
}
