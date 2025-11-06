<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Diagnostic extends Model
{
    protected $table = 'diagnostics';
    protected $fillable = [
        'lead_id',
        'diagnosed_by_id',
        'problem_description',
        'customer_needs',
        'possible_solutions',
        'urgency_level'
    ];

    public function lead(): BelongsTo {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function diagnosedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'diagnosed_by_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
