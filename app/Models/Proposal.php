<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Proposal extends Model
{
    protected $table = 'proposals';

    protected $fillable = [
        'lead_id',
        'created_by',
        'title',
        'body',
        'total_value',
        'valid_until',
        'status',
        'sent_at'
    ];

    public function lead(): BelongsTo {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function createdBy(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
