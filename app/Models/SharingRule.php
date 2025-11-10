<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'sharing_setting_id',
        'target_id',
        'target_type',
    ];

    public function sharingSetting()
    {
        return $this->belongsTo(SharingSetting::class);
    }
}
