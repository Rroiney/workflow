<?php

use App\Models\ActivityLog;

if (!function_exists('activity_log')) {
    function activity_log(
        string $action,
        string $description = null,
        $subject = null
    ) {
        ActivityLog::create([
            'user_id' => auth('tenant')->id(),
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'description' => $description,
        ]);
    }
}
