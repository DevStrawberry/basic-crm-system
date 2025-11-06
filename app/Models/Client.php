<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function contactSource(): BelongsTo {
        return $this->belongsTo(ContactSource::class, 'contact_source_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
