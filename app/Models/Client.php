<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'cpf',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'owner_user_id',
        'contact_source_id'
        ];

    public function owner(): BelongsTo {
        return $this->belongsTo(User::class, 'id', 'owner_user_id');
    }

    public function leads(): HasMany {
        return $this->hasMany(Lead::class, 'client_id');
    }

    public function contactSource(): HasOne {
        return $this->hasOne(ContactSource::class, 'id', 'contact_source_id');
    }

    public function socialNetworks(): belongsToMany
    {
        return $this->belongsToMany(SocialNetwork::class, 'client_social_network')
            ->withPivot('profile_url');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
