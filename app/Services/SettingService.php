<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Setting;

class SettingService
{
    public function all(): array
    {
        return Setting::query()->pluck('value', 'key')->all();
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default);
    }

    public function update(array $data, Admin $admin): void
    {
        $allowed = [
            'store_name',
            'store_phone',
            'store_address',
            'order_prefix',
            'low_stock_threshold',
            'store_note',
        ];

        $before = $this->all();

        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                Setting::put($key, $data[$key], 'store');
            }
        }

        app(AuditLogService::class)->record('settings.updated', null, $before, $this->all(), $admin);
    }
}
