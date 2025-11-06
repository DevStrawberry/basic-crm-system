<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialNetwork extends Model
{
    protected $table = 'social_networks';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function clients(): belongsToMany
    {
        return $this->belongsToMany(Client::class)
            ->withPivot('profile_url');
    }
}
