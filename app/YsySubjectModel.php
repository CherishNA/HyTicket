<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YsySubjectModel extends Model
{
    //
    protected $table = 'ysy_subject';
    protected $fillable = ['subname', 'status'];
}
