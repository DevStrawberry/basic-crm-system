<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Interaction extends Model
{
    protected $table = 'interactions';

    protected $fillable = [
        'lead_id',
        'client_id',
        'contract_id',
        'created_by',
        'type',
        'subject',
        'body',
        'happened_at'
    ];

    public function lead(): BelongsTo {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

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
}
