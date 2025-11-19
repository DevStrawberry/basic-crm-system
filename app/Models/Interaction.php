<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Interaction extends Model
{
    protected $table = 'interactions';

    protected $fillable = [
        'related_table',
        'related_id',
        'client_id',
        'contract_id',
        'created_by',
        'type',
        'subject',
        'body',
        'happened_at'
    ];

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function contract(): BelongsTo {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function createdBy(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }

    public function related(): morphTo {
        return $this->morphTo(__FUNCTION__, 'related_table', 'related_id');
    }
}
