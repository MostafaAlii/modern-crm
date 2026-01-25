<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class RefreshToken extends BaseModel {
    protected $table = 'refresh_tokens';
    protected $fillable = [
        'uuid',
        'tokenable_id',
        'tokenable_type',
        'guard',
        'token',
        'expires_at',
        'revoked',
        'device_name',
        'ip_address',
        'last_used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function tokenable(): MorphTo {
        return $this->morphTo();
    }

    public function isExpired(): bool {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool {
        return !$this->revoked && !$this->isExpired();
    }

    public function revoke(): bool {
        $this->revoked = true;
        return $this->save();
    }

    public function updateLastUsed(): bool {
        $this->last_used_at = now();
        return $this->save();
    }

    public function scopeValid($query) {
        return $query->where('revoked', false)
            ->where('expires_at', '>', now());
    }

    public function scopeForGuard($query, string $guard) {
        return $query->where('guard', $guard);
    }

    public function scopeForTokenable($query, $tokenable, string $guard) {
        return $query->where('tokenable_id', $tokenable->id)
            ->where('tokenable_type', get_class($tokenable))
            ->where('guard', $guard);
    }

    public function scopeByUuid($query, string $uuid) {
        return $query->where('uuid', $uuid);
    }
}
