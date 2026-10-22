<?php

namespace App\Support;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserAccountAccess
{
    public static function mayUpdate(?User $actor, ?User $target, Request $request, bool $selfService = false): bool
    {
        if (! $actor || ! $target || $actor->status !== 'Active' || ! self::scalarAuthority($request)) {
            return false;
        }

        // The API also serves profile edits. These may change profile fields,
        // but must never change the caller's authority or tenant membership.
        if ($selfService && $actor->is($target)) {
            foreach (['access_level', 'status', 'regional_code', 'assembly_code', 'division_code', 'role_access', 'pro_owner_uuid'] as $field) {
                if ($request->exists($field) && (string) $request->input($field) !== (string) $target->getAttribute($field)) {
                    return false;
                }
            }

            return ! $request->exists('role');
        }

        if (! self::hasManagementPermission($actor, 'users.update')) {
            return false;
        }

        // Super_User is the existing global administrator contract used by
        // the user-management screen. Other administrators retain its scopes.
        if ($actor->access_level === 'Super_User') {
            return true;
        }

        foreach (['regional_code', 'assembly_code', 'division_code'] as $field) {
            if (filled($actor->getAttribute($field))) {
                if ((string) $actor->getAttribute($field) !== (string) $target->getAttribute($field)
                    || ($request->exists($field) && (string) $request->input($field) !== (string) $actor->getAttribute($field))) {
                    return false;
                }
            }
        }

        if ($target->access_level === 'Super_User' || $request->input('access_level') === 'Super_User') {
            return false;
        }

        if (str_contains((string) $actor->access_level, 'Assembly')) {
            $assemblyLevels = ['Assembly_Administrator', 'Assembly_Supervisor', 'Assembly_Agent', 'customer'];
            if (! in_array($target->access_level, $assemblyLevels, true)
                || ! in_array($request->input('access_level', $target->access_level), $assemblyLevels, true)) {
                return false;
            }
        }

        // Enforce the same privileged-role exclusions shown in the admin UI.
        $restrictedRoles = ['administrator', 'gra', 'gog'];
        if ($target->roles->contains(fn ($role) => in_array(strtolower($role->name), $restrictedRoles, true))) {
            return false;
        }
        if ($request->filled('role')) {
            $role = Role::query()->find($request->input('role'));
            if (! $role || ! $role->active || in_array(strtolower($role->name), $restrictedRoles, true)) {
                return false;
            }
        }

        return true;
    }

    public static function mayCreate(?User $actor, Request $request): bool
    {
        if (! self::hasManagementPermission($actor, 'users.create') || ! self::scalarAuthority($request)) {
            return false;
        }
        if ($actor->access_level === 'Super_User') {
            return true;
        }
        foreach (['regional_code', 'assembly_code', 'division_code'] as $field) {
            if (filled($actor->getAttribute($field)) && $request->exists($field)
                && (string) $request->input($field) !== (string) $actor->getAttribute($field)) {
                return false;
            }
        }
        if ($request->input('access_level') === 'Super_User') {
            return false;
        }
        if (str_contains((string) $actor->access_level, 'Assembly')
            && ! in_array($request->input('access_level'), ['Assembly_Administrator', 'Assembly_Supervisor', 'Assembly_Agent', 'customer'], true)) {
            return false;
        }
        if ($request->filled('role')) {
            $role = Role::query()->find($request->input('role'));
            if (! $role || ! $role->active || in_array(strtolower($role->name), ['administrator', 'gra', 'gog'], true)) {
                return false;
            }
        }

        return true;
    }

    public static function hasManagementPermission(?User $actor, string $permission): bool
    {
        return $actor && $actor->status === 'Active' && $actor->can($permission)
            && ($actor->hasDirectPermission($permission)
                || $actor->roles()->where('active', true)->whereHas('permissions', fn ($query) => $query->where('name', $permission))->exists());
    }

    public static function visibleQuery(Builder $query, User $actor): Builder
    {
        abort_unless($actor->status === 'Active', 403);
        if (! self::hasManagementPermission($actor, 'users.view')) {
            return $query->whereKey($actor->getKey());
        }
        if ($actor->access_level !== 'Super_User') {
            foreach (['regional_code', 'assembly_code', 'division_code'] as $field) {
                if (filled($actor->getAttribute($field))) {
                    $query->where($field, $actor->getAttribute($field));
                }
            }
        }

        return $query;
    }

    public static function mayView(User $actor, User $target): bool
    {
        return self::visibleQuery(User::query(), $actor)->whereKey($target->getKey())->exists();
    }

    private static function scalarAuthority(Request $request): bool
    {
        foreach (['access_level', 'status', 'regional_code', 'assembly_code', 'division_code', 'role_access', 'pro_owner_uuid', 'role'] as $field) {
            if ($request->exists($field) && $request->input($field) !== null && ! is_scalar($request->input($field))) {
                return false;
            }
        }

        return true;
    }

    public static function writableApiFields(User $actor, User $target, array $data): array
    {
        if ($actor->is($target)) {
            return array_intersect_key($data, array_flip(['name', 'email', 'phone', 'password']));
        }

        return $data;
    }
}
