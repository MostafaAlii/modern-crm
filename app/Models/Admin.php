<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Admin\{AdminStatus, AdminType};
use Tymon\JWTAuth\Contracts\JWTSubject;
class Admin extends Authenticatable implements JWTSubject {
    use HasFactory, Notifiable;
    protected $table = 'admins';
    protected $fillable = ['name', 'email', 'password', 'status', 'type'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'status' => AdminStatus::class,
        'type' => AdminType::class,
    ];

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    public function getJWTCustomClaims() {
        return [];
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool {
        return $this->status === AdminStatus::ACTIVE->value;
    }
}
