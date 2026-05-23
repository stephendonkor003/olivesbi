<?php

namespace App\Traits;

use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Traits\HasRoles;

trait HasActiveRoles
{
    use HasRoles;

    /**
     * Determine if the model has (one of) the given role(s).
     *
     * @param  string|int|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection  $roles
     */
    public function hasRole($roles, ?string $guard = null): bool
    {
        $this->loadMissing('roles', 'roles.permissions');

        if (is_string($roles) && str_contains($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $guard
                ? $this->roles->where('active', true)->where('guard_name', $guard)->contains('name', $roles)
                : $this->roles->where('active', true)->contains('name', $roles);
        }

        if (is_int($roles)) {
            $roleClass = $this->getRoleClass();
            $key = (new $roleClass)->getKeyName();

            return $guard
                ? $this->roles->where('active', true)->where('guard_name', $guard)->contains($key, $roles)
                : $this->roles->where('active', true)->contains($key, $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->where('active', true)->contains($roles->getKeyName(), $roles->getKey());
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role, $guard)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($guard ? $this->roles->where('active', true)->where('guard_name', $guard) : $this->roles->where('active', true))->isNotEmpty();
    }

    /**
     * Determine if the model has all of the given role(s).
     *
     * @param  string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection  $roles
     */
    public function hasAllRoles($roles, ?string $guard = null): bool
    {
        $this->loadMissing('roles');

        if (is_string($roles) && str_contains($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $guard
                ? $this->roles->where('active', true)->where('guard_name', $guard)->contains('name', $roles)
                : $this->roles->where('active', true)->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->where('active', true)->contains($roles->getKeyName(), $roles->getKey());
        }

        $roles = collect()->make($roles)->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $roles->intersect(
            $guard
                ? $this->roles->where('active', true)->where('guard_name', $guard)->pluck('name')
                : $this->getRoleNames()
        ) == $roles;
    }
}
