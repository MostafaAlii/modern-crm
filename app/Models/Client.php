<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\Client\ClientStatus;
class Client extends Authenticatable {
    use HasFactory, Notifiable;
    protected $table = 'clients';
    protected $fillable = ['name', 'email', 'password', 'status'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'status' => ClientStatus::class,
    ];

    public function isActive(): bool
    {
        return $this->status === ClientStatus::ACTIVE->value;
    }

    public function isBlocked(): bool
    {
        return $this->status === ClientStatus::BLOCKED->value;
    }
}