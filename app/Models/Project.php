<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'type',            // landing_page, blog, ecommerce, saas, custom
        'status',          // draft, building, ready, deployed
        'framework',       // laravel (default)
        'ftp_host',
        'ftp_username',
        'ftp_password',    // encrypted
        'ftp_port',
        'ftp_remote_path',
        'db_host',
        'db_name',
        'db_username',
        'db_password',     // encrypted
        'domain',
        'deployed_at',
        'settings',        // JSON: theme, colors, fonts etc
        'ai_context',      // JSON: previous conversation context
    ];

    protected $casts = [
        'settings'    => 'array',
        'ai_context'  => 'array',
        'deployed_at' => 'datetime',
    ];

    protected $hidden = [
        'ftp_password',
        'db_password',
    ];

    // ─── RELATIONSHIPS ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function chatHistory()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class)->latest();
    }

    // ─── SCOPES ───────────────────────────────────────────────────

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeDeployed($query)
    {
        return $query->where('status', 'deployed');
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function isDeployed(): bool
    {
        return $this->status === 'deployed';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft'    => '<span class="badge badge-gray">Draft</span>',
            'building' => '<span class="badge badge-yellow">Building...</span>',
            'ready'    => '<span class="badge badge-blue">Ready</span>',
            'deployed' => '<span class="badge badge-green">Live</span>',
            default    => '<span class="badge badge-gray">Unknown</span>',
        };
    }
}
