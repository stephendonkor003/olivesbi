<?php

namespace App\Support;

use App\Models\Assembly;
use App\Models\Bill;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class OwnerRecordAccess
{
    public static function isSelf(User $actor, Owner $owner): bool
    {
        $sameAccount = filled($actor->erms_id) && (string) $actor->erms_id === (string) $owner->erms_id;
        $hasUser = filled($owner->user_id) && (string) $owner->user_id !== '0';
        $sameUser = $hasUser && (string) $owner->user_id === (string) $actor->id;
        if (filled($actor->erms_id) && filled($owner->erms_id) && ! $sameAccount) {
            return false;
        }
        if ($hasUser && ! $sameUser) {
            return false;
        }

        return $sameAccount || $sameUser;
    }

    private static function own(Builder $query, User $actor): Builder
    {
        $model = $query->getModel();
        $hasUserId = $model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'user_id');

        return $query->where(function (Builder $identity) use ($actor, $model, $hasUserId) {
            $identity->whereRaw('1 = 0');
            if (filled($actor->erms_id)) {
                $identity->orWhere(function (Builder $account) use ($actor, $model, $hasUserId) {
                    $account->where($model->qualifyColumn('erms_id'), $actor->erms_id);
                    if ($hasUserId) {
                        $account->where(fn (Builder $link) => $link->whereNull($model->qualifyColumn('user_id'))
                            ->orWhere($model->qualifyColumn('user_id'), '0')
                            ->orWhere($model->qualifyColumn('user_id'), (string) $actor->id));
                    }
                });
            }
            if ($hasUserId) {
                $identity->orWhere(function (Builder $user) use ($actor, $model) {
                    $user->where($model->qualifyColumn('user_id'), (string) $actor->id);
                    if (filled($actor->erms_id)) {
                        $user->where(fn (Builder $account) => $account->whereNull($model->qualifyColumn('erms_id'))
                            ->orWhere($model->qualifyColumn('erms_id'), $actor->erms_id));
                    }
                });
            }
        });
    }

    private static function tenant(Builder $query, User $actor): Builder
    {
        if ($actor->access_level === 'Super_User' || (! filled($actor->assembly_code) && ! filled($actor->regional_code))) {
            return $query->whereRaw('1 = 1');
        }
        $column = $query->getModel()->qualifyColumn('assembly_code');
        if (filled($actor->assembly_code)) {
            $query->where($column, $actor->assembly_code);
        }
        if (filled($actor->regional_code)) {
            $query->where(function (Builder $region) use ($actor, $column) {
                $region->whereIn($column, Assembly::query()->select('assembly_code')->where('regional_code', $actor->regional_code));
                if ($region->getModel() instanceof Owner) {
                    $region->orWhere(fn (Builder $unassigned) => $unassigned->whereNull($column)
                        ->where($region->getModel()->qualifyColumn('regional_code'), $actor->regional_code));
                }
            });
        }

        return $query;
    }

    public static function visibleOwners(Builder|Relation $query, User $actor): Builder
    {
        $query = $query instanceof Relation ? $query->getQuery() : $query;
        abort_unless($actor->status === 'Active', 403);

        return $query->where(function (Builder $access) use ($actor) {
            $access->where(fn (Builder $own) => self::own($own, $actor));
            if (UserAccountAccess::hasManagementPermission($actor, 'customers.view')) {
                $access->orWhere(fn (Builder $tenant) => self::tenant($tenant, $actor))
                    ->orWhereHas('properties', fn (Builder $assets) => self::tenant($assets, $actor))
                    ->orWhereHas('businesses', fn (Builder $assets) => self::tenant($assets, $actor));
            }
        });
    }

    public static function visibleAssets(Builder|Relation $query, User $actor): Builder
    {
        $query = $query instanceof Relation ? $query->getQuery() : $query;
        abort_unless($actor->status === 'Active', 403);

        return $query->where(function (Builder $access) use ($actor) {
            $access->whereRaw('1 = 0');
            if (UserAccountAccess::hasManagementPermission($actor, 'customers.view')) {
                $access->orWhere(fn (Builder $tenant) => self::tenant($tenant, $actor));
            }
            if ($access->getModel() instanceof Payment) {
                $access->orWhereHas('bill.property.customer', fn (Builder $owner) => self::own($owner, $actor))
                    ->orWhereHas('bill.business.customer', fn (Builder $owner) => self::own($owner, $actor));
            } elseif ($access->getModel() instanceof Bill) {
                $access->orWhereHas('property.customer', fn (Builder $owner) => self::own($owner, $actor))
                    ->orWhereHas('business.customer', fn (Builder $owner) => self::own($owner, $actor));
            } else {
                $access->orWhereHas('customer', fn (Builder $owner) => self::own($owner, $actor));
            }
        });
    }

    private static function scalarScope(Request $request): bool
    {
        foreach (['assembly_code', 'regional_code', 'division_code'] as $field) {
            if ($request->exists($field) && $request->input($field) !== null && ! is_scalar($request->input($field))) {
                return false;
            }
        }

        return true;
    }

    private static function mayAssignAssembly(User $actor, mixed $code): bool
    {
        if ($actor->access_level === 'Super_User') {
            return true;
        }
        if (filled($actor->assembly_code) && (string) $code !== (string) $actor->assembly_code) {
            return false;
        }

        return ! filled($code) || ! filled($actor->regional_code)
            || Assembly::query()->where('assembly_code', $code)->where('regional_code', $actor->regional_code)->exists();
    }

    public static function mayCreate(?User $actor, Request $request): bool
    {
        return UserAccountAccess::hasManagementPermission($actor, 'customers.create') && self::scalarScope($request)
            && self::mayAssignAssembly($actor, $request->input('assembly_code', $actor->assembly_code));
    }

    public static function mayUpdate(?User $actor, ?Owner $owner, Request $request): bool
    {
        if (! $actor || ! $owner || $actor->status !== 'Active' || ! self::scalarScope($request)) {
            return false;
        }
        if (self::isSelf($actor, $owner)) {
            foreach (['assembly_code', 'regional_code', 'division_code'] as $field) {
                if ($request->exists($field) && (string) $request->input($field) !== (string) $owner->getAttribute($field)) {
                    return false;
                }
            }

            return true;
        }
        if (! UserAccountAccess::hasManagementPermission($actor, 'customers.update')
            || ! self::tenant(Owner::query(), $actor)->whereKey($owner->id)->exists()) {
            return false;
        }

        return ! $request->exists('assembly_code') || self::mayAssignAssembly($actor, $request->input('assembly_code'));
    }
}
