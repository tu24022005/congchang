<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public static function record(
        string $action,
        string $description,
        ?Model $subject = null,
        ?array $before = null,
        ?array $after = null,
        array $metadata = []
    ): ?ActivityLog {
        $actorId = Auth::id();

        if (!$actorId) {
            return null;
        }

        return ActivityLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'before' => $before,
            'after' => $after,
            'metadata' => $metadata ?: null,
        ]);
    }
}
