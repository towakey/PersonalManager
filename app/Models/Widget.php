<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'settings',
        'position',
    ];

    protected $casts = [
        'settings' => 'array',
        'position' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sharingSetting()
    {
        return $this->hasOne(SharingSetting::class);
    }

    public function sharingRules()
    {
        return $this->hasManyThrough(SharingRule::class, SharingSetting::class);
    }
}
