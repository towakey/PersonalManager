<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'widget_id',
        'sharing_type',
        'access_token',
    ];

    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }

    public function sharingRules()
    {
        return $this->hasMany(SharingRule::class);
    }
}
