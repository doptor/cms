<?php
// ═══════════════════════════════════════════════════════════
// FILE: app/Models/ChatMessage.php
// ═══════════════════════════════════════════════════════════
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = [
        'project_id', 'user_id', 'role', // user | assistant
        'content', 'model_used', 'tokens_used', 'files_generated',
    ];

    protected $casts = ['files_generated' => 'array'];

    public function project() { return $this->belongsTo(Project::class); }
    public function user()    { return $this->belongsTo(User::class); }
}
