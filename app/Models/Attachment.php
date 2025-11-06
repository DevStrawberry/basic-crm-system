<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $table = 'attachments';

    protected $fillable = [
        'related_type',
        'related_id',
        'filename',
        'file_path',
        'content_type',
        'uploaded_by'
    ];

    public function uploadedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function related(): morphTo {
        return $this->morphTo(__FUNCTION__, 'related_table', 'related_id');
    }
}
