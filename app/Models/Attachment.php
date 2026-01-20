<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Attachment extends Model
{
    protected $fillable = [
        'workspace_id',
        'item_id',
        'uploaded_by',
        'file_path',
        'mime_type',
        'size',
    ];

    protected static function booted(): void
    {
        // Global scope for tenant isolation
        static::addGlobalScope('workspace', function (Builder $builder) {
            $user = auth()->user();
            
            if (!$user) {
                return;
            }

            if ($user->isSystemAdmin()) {
                return;
            }

            $builder->where('attachments.workspace_id', $user->workspace_id);
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}