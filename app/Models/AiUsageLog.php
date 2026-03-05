<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'model',
        'input_tokens', 'output_tokens', 'total_tokens', 'prompt_summary',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function project() { return $this->belongsTo(Project::class); }
}
