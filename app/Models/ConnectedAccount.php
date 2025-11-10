<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectedAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_name',
        'access_token',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAccessToken(): string
    {
        return decrypt($this->access_token);
    }

    public function setAccessToken(string $token): void
    {
        $this->access_token = encrypt($token);
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token ? decrypt($this->refresh_token) : null;
    }

    public function setRefreshToken(?string $token): void
    {
        $this->refresh_token = $token ? encrypt($token) : null;
    }
}
