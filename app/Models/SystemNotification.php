<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemNotification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'icon',
        'target_users',
        'target_roles',
        'is_active',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_users' => 'array',
        'target_roles' => 'array',
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->is_active && 
               ($this->expires_at === null || now()->lt($this->expires_at));
    }

    public function isTargetedToUser(User $user): bool
    {
        if ($this->target_users === null && $this->target_roles === null) {
            return true;
        }

        if ($this->target_users !== null && in_array($user->id, $this->target_users)) {
            return true;
        }

        if ($this->target_roles !== null && $user->hasAnyRole($this->target_roles)) {
            return true;
        }

        return false;
    }
} 