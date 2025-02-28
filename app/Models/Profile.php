<?php

namespace App\Models;

use App\Traits\CacheClearable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory, CacheClearable;

    protected $fillable = [
        'biography',
        'receive_notifications',
        'profile_picture',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->profile_picture);
    }
}
