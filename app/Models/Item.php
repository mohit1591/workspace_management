<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workspace_id',
        'assigned_user_id',
        'title',
        'status',
    ];

    protected static function booted(): void
    {
        // Global scope for tenant isolation (except system_admin)
        static::addGlobalScope('workspace', function (Builder $builder) {
            $user = auth()->user();
            
            if (!$user) {
                return;
            }

            // System admin can see all workspaces
            if ($user->isSystemAdmin()) {
                return;
            }

            // Workspace admin and members see only their workspace
            $builder->where('items.workspace_id', $user->workspace_id);
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

}