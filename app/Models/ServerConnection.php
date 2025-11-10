<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServerConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'server_identifier',
        'status',
        'api_token',
    ];

    public function getApiToken(): ?string
    {
        return $this->api_token ? decrypt($this->api_token) : null;
    }

    public function setApiToken(?string $token): void
    {
        $this->api_token = $token ? encrypt($token) : null;
    }
}
