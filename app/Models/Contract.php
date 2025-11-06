<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contract extends Model
{
    protected $table = 'contracts';

    protected $fillable = [
        'lead_id',
        'proposal_id',
        'contract_number',
        'final_value',
        'payment_method',
        'signed_by',
        'signed_at',
        'file_path'
    ];

    public function proposal(): BelongsTo {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function lead(): BelongsTo {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
