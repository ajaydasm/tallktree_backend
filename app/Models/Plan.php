<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    
    protected $table = 'plans';

    protected $fillable = ['name', 'price', 'total_minutes','image','status'];
}
