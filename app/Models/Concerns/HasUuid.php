<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait HasUuid
{
    protected static array $uuidColumnCache = [];

    protected static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if ($model->hasUuidColumn() && empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return $this->hasUuidColumn() ? 'uuid' : parent::getRouteKeyName();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field || ! $this->hasUuidColumn()) {
            return parent::resolveRouteBinding($value, $field);
        }

        return $this->newQuery()->whereUuidOrId($value)->first();
    }

    public function scopeWhereUuidOrId($query, $value)
    {
        return $query->where(function ($query) use ($value) {
            $hasCondition = false;

            if ($this->hasUuidColumn()) {
                $query->where('uuid', $value);
                $hasCondition = true;
            }

            if (ctype_digit((string) $value)) {
                $method = $hasCondition ? 'orWhere' : 'where';
                $query->{$method}($this->getKeyName(), $value);
                $hasCondition = true;
            }

            if (! $hasCondition) {
                $query->whereRaw('1 = 0');
            }
        });
    }

    protected function hasUuidColumn(): bool
    {
        $table = $this->getTable();

        if (! array_key_exists($table, static::$uuidColumnCache)) {
            static::$uuidColumnCache[$table] = Schema::hasTable($table)
                && Schema::hasColumn($table, 'uuid');
        }

        return static::$uuidColumnCache[$table];
    }
}
