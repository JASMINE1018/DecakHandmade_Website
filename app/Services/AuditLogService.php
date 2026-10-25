<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AuditLogService
{
    public function record(string $action, ?Model $subject = null, ?array $before = null, ?array $after = null, ?Admin $actor = null): AuditLog
    {
        $actor ??= auth('admin')->user();

        $requestId = request()->attributes->get('audit_request_id');

        if (! $requestId) {
            $requestId = (string) Str::uuid();
            request()->attributes->set('audit_request_id', $requestId);
        }

        return AuditLog::create([
            'admin_id' => $actor?->getKey(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'before' => $before,
            'after' => $after,
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 255, ''),
            'request_id' => $requestId,
            'created_at' => now(),
        ]);
    }
}
