<?php

namespace App\Models;

use App\Traits\CacheClearable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommentReply extends Model
{
    use HasFactory, CacheClearable;

    protected $fillable = [
        'content',
        'user_id',
        'comment_id',
        'parent_reply_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function parentReply(): BelongsTo
    {
        return $this->belongsTo(CommentReply::class, 'parent_reply_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(CommentReply::class, 'parent_reply_id');
    }
}
