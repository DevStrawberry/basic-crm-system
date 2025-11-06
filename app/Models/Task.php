<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'lead_id',
        'client_id',
        'assigned_to',
        'title',
        'description',
        'due_date',
        'completed'
    ];

    public function lead(): BelongsTo {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function client(): BelongsTo {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function assignedTo(): BelongsTo {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
