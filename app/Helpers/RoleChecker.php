<?php

namespace App\Helpers;

use App\Models\User;

/**
 * Class RoleChecker
 * @package App\Role
 */
class RoleChecker
{
    /**
     * @param User $user
     * @param string $role
     * @return bool
     */
    public static function check(User $user, string $role): bool
    {
        // Admin has everything
        if ($user->hasRole(UserRole::ROLE_ADMIN)) {
            return true;
        } else if ($user->hasRole(UserRole::ROLE_MANAGEMENT)) {
            $managementRoles = UserRole::getAllowedRoles(UserRole::ROLE_MANAGEMENT);

            if (in_array($role, $managementRoles)) {
                return true;
            }
        } else if ($user->hasRole(UserRole::ROLE_ACCOUNT_MANAGER)) {
            $accountManagertRoles = UserRole::getAllowedRoles(UserRole::ROLE_ACCOUNT_MANAGER);

            if (in_array($role, $accountManagertRoles)) {
                return true;
            }
        } else if ($user->hasRole(UserRole::ROLE_FINANCE)) {
            $financeRoles = UserRole::getAllowedRoles(UserRole::ROLE_FINANCE);

            if (in_array($role, $financeRoles)) {
                return true;
            }
        }

        return $user->hasRole($role);
    }
}
