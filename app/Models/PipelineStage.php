<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PipelineStage extends Model
{
    public $timestamps = false;
    protected $table = 'pipeline_stages';
    protected $fillable = [ 'name', 'ordering', 'active' ];

    public function leads(): HasMany {
        return $this->hasMany(Lead::class, 'pipeline_stage_id');
    }
}
