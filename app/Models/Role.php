<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';
    public $timestamps = false;

    protected $fillable = [ 'name' ];

    public function users() : hasMany {
        return $this->hasMany(User::class, 'role_id');
    }
}
