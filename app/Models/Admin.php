<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Admin\{AdminStatus};
class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'admins';
    //protected $guard = 'admin';
    protected $fillable = ['name', 'email', 'password', 'status'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'status' => AdminStatus::class,
    ];

    public function isActive(): bool {
        return $this->status === AdminStatus::ACTIVE->value;
    }
}
