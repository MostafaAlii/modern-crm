<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class RefreshToken extends BaseModel {
    protected $table = 'refresh_tokens';
    protected $fillable = [
        'uuid',
        'token_id',
        'access_token_id',
        'user_id',
        'user_type',
        'device_info',
        'ip_address',
        'user_agent',
        'location',
        'last_used_at',
        'usage_count',
        'expires_at',
        'is_revoked',
        'revoked_at',
        'revoked_by',
        'metadata'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_revoked' => 'boolean',
        'usage_count' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): MorphTo {
        return $this->morphTo();
    }

    public function isExpired(): bool {
        return $this->expires_at->isPast();
    }

    public function isValid(): bool {
        return !$this->is_revoked && !$this->isExpired();
    }

    public function revoke(?int $revokedBy = null): bool {
        $this->update([
            'is_revoked' => true,
            'revoked_at' => now(),
            'revoked_by' => $revokedBy,
        ]);
        return true;
    }

    public function markAsUsed(): bool {
        return $this->update([
            'last_used_at' => now(),
            'usage_count' => $this->usage_count + 1,
        ]);
    }

    public function scopeValid($query) {
        return $query->where('is_revoked', false)->where('expires_at', '>', now());
    }

    public function scopeRevoked($query) {
        return $query->where('is_revoked', true);
    }

    public function scopeExpired($query) {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeForUser($query, $userId, $userType) {
        return $query->where('user_id', $userId)->where('user_type', $userType);
    }

    public function scopeByDevice($query, $deviceInfo) {
        return $query->where('device_info', $deviceInfo);
    }

    public function scopeByIp($query, $ipAddress) {
        return $query->where('ip_address', $ipAddress);
    }
}
