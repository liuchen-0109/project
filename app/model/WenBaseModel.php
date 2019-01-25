<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class WenBaseModel extends Model
{
    protected $connection = 'wen_mysql';
    public $timestamps = false;
    protected $guarded  = [];
}
