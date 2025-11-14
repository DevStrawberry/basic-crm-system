<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\User;

class Diagnostic extends Model
{
    use SoftDeletes;

    protected $table = 'diagnostics';

    protected $fillable = [
        'lead_id',
        'diagnosed_by_id',
        'problem_description',
        'customer_needs',
        'possible_solutions',
        'urgency_level'
    ];

    protected $dates = ['deleted_at'];


    /**
     * Relacionamento: O diagnóstico pertence a um Lead.
     */
    public function lead(): BelongsTo {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * Relacionamento: O diagnóstico foi feito por um Usuário (Assessor/Vendedor).
     */
    public function diagnosedBy(): BelongsTo {
        return $this->belongsTo(User::class, 'diagnosed_by_id');
    }

    /**
     * Relacionamento MorphMany: Permite anexar documentos (Anexar documento - Use Case).
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related', 'related_table', 'related_id');
    }
}
