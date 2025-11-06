<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostReason extends Model
{
    public $timestamps = false;
    protected $table = 'lost_reasons';
    protected $fillable = [ 'description' ];
}
