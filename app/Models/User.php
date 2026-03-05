<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Services\EncryptionService;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'claude_api_key',
        'deepseek_api_key',
        'ai_routing_mode',
        'is_active',
        'last_login_at',
        'timezone',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'claude_api_key',
        'deepseek_api_key',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_active'         => 'boolean',
        'password'          => 'hashed',
    ];

    // ─── RELATIONSHIPS ────────────────────────────────────────────

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function aiUsageLogs()
    {
        return $this->hasMany(AiUsageLog::class);
    }

    public function marketplaceItems()
    {
        return $this->hasMany(MarketplaceItem::class);
    }

    // ─── API KEY ACCESSORS (decrypted on the fly) ─────────────────

    public function getClaudeApiKeyDecryptedAttribute(): ?string
    {
        if (empty($this->claude_api_key)) return null;
        return app(EncryptionService::class)->decrypt($this->claude_api_key);
    }

    public function getDeepseekApiKeyDecryptedAttribute(): ?string
    {
        if (empty($this->deepseek_api_key)) return null;
        return app(EncryptionService::class)->decrypt($this->deepseek_api_key);
    }

    public function getClaudeApiKeyMaskedAttribute(): ?string
    {
        $decrypted = $this->claude_api_key_decrypted;
        if (!$decrypted) return null;
        return app(EncryptionService::class)->mask($decrypted);
    }

    public function getDeepseekApiKeyMaskedAttribute(): ?string
    {
        $decrypted = $this->deepseek_api_key_decrypted;
        if (!$decrypted) return null;
        return app(EncryptionService::class)->mask($decrypted);
    }

    // ─── HELPERS ──────────────────────────────────────────────────

    public function hasClaudeKey(): bool
    {
        return !empty($this->claude_api_key);
    }

    public function hasDeepSeekKey(): bool
    {
        return !empty($this->deepseek_api_key);
    }

    public function hasAnyAiKey(): bool
    {
        return $this->hasClaudeKey() || $this->hasDeepSeekKey();
    }

    public function todayTokenUsage(): int
    {
        return $this->aiUsageLogs()
            ->whereDate('created_at', today())
            ->sum('total_tokens');
    }

    public function monthTokenUsage(): int
    {
        return $this->aiUsageLogs()
            ->whereMonth('created_at', now()->month)
            ->sum('total_tokens');
    }
}
