<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lead extends Model
{
    protected $table = 'leads';

    protected $fillable = [
        'title',
        'description',
        'estimated_value',
        'is_won',
        'client_id',
        'owner_id',
        'pipeline_stage_id',
        'lost_reason_id'
    ];

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function owner(): BelongsTo {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function pipelineStage(): BelongsTo {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function lostReason(): BelongsTo {
        return $this->belongsTo(LostReason::class, 'lost_reason_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
