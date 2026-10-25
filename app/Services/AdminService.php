<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Validation\ValidationException;

class AdminService
{
    public function __construct(private readonly AuditLogService $audit) {}

    public function create(array $data, Admin $actor): Admin
    {
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'admin',
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $this->audit->record('admin.created', $admin, null, $admin->only(['name', 'email', 'is_active']), $actor);

        return $admin;
    }

    public function update(Admin $admin, array $data, Admin $actor): Admin
    {
        $before = $admin->only(['name', 'email', 'is_active']);

        $admin->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        if (! empty($data['password'])) {
            $admin->password = $data['password'];
        }

        $admin->save();

        $this->audit->record('admin.updated', $admin, $before, $admin->only(array_keys($before)), $actor);

        return $admin;
    }

    public function deactivate(Admin $admin, Admin $actor): void
    {
        if ($admin->is($actor)) {
            throw ValidationException::withMessages(['admin' => 'Tidak dapat menonaktifkan akun sendiri.']);
        }

        if ($admin->is_active && $admin->isLastActiveAdmin()) {
            throw ValidationException::withMessages(['admin' => 'Minimal satu admin aktif harus tersedia.']);
        }

        $admin->update(['is_active' => false]);

        $this->audit->record('admin.deactivated', $admin, ['is_active' => true], ['is_active' => false], $actor);
    }
}
