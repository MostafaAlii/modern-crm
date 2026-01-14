<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Company extends BaseModel {
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'website',
        'status',
        'client_id',
    ];

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}