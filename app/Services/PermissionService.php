<?php

namespace App\Services;

use App\Enums\Users\UserRole;

class PermissionService
{
    public function haveAdminOrHRPermission()
    {
        $currentUser = auth()->user();
        if ($currentUser->role === UserRole::ADMIN->value || $currentUser->role === UserRole::HR->value) {
            return true;
        }
        return false;
    }

    public function haveOnlyAdminPermission()
    {
        $currentUser = auth()->user();
        if ($currentUser->role === UserRole::ADMIN->value) {
            return true;
        }
        return false;
    }

    public function haveOnlyHRPermission()
    {
        $currentUser = auth()->user();
        if ($currentUser->role === UserRole::HR->value) {
            return true;
        }
        return false;
    }
}
