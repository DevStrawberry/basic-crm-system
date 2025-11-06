<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactSource extends Model
{
    public $timestamps = false;
    protected $table = 'contact_sources';
    protected $fillable = [ 'description' ];

    public function clients(): HasMany {
        return $this->hasMany(Client::class, 'contact_source_id');
    }
}
