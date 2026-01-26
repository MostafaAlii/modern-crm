<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Client\ClientStatus;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Concerns\HasRefreshToken;
class Client extends Authenticatable implements JWTSubject {
    use HasFactory, Notifiable, HasRefreshToken;
    protected $table = 'clients';
    protected $fillable = ['name', 'email', 'password', 'status'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'status' => ClientStatus::class,
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isActive(): bool
    {
        return $this->status === ClientStatus::ACTIVE->value;
    }

    public function isBlocked(): bool
    {
        return $this->status === ClientStatus::BLOCKED->value;
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
