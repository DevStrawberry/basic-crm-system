<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes;

    protected $table = 'contracts';

    protected $fillable = [
        'lead_id',
        'proposal_id',
        'assigned_to',
        'contract_number',
        'status',
        'final_value',
        'payment_method',
        'deadline',
        'signed_by',
        'signed_at',
        'file_path',
        'notes'
    ];

    protected $casts = [
        'deadline' => 'date',
        'signed_at' => 'date',
    ];

    public function proposal(): BelongsTo {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function lead(): BelongsTo {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function assignedTo(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function interactions(): HasMany {
        return $this->hasMany(Interaction::class, 'contract_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
