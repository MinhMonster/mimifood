<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, ?array $filters = null): Builder
    {
        $filters = $filters ?? request()->all();

        foreach ($this->filterableFields() as $key => $config) {
            if (
                !array_key_exists($key, $filters) ||
                $filters[$key] === null ||
                $filters[$key] === ''
            ) {
                continue;
            }

            $value = $filters[$key];

            // Custom filter (closure)
            if (is_callable($config)) {
                $config($query, $value, $filters);
                continue;
            }

            // Normalize config
            $column   = $config[0];
            $operator = $config[1] ?? '=';

            if ($operator === 'like') {
                $query->where($column, 'like', "%{$value}%");
            } elseif ($operator === 'range') {
                apply_range_filter($query, $column, $value);
            } elseif ($operator === 'in') {
                $query->whereIn($column, (array) $value);
            } else {
                $query->where($column, $operator, $value);
            }
        }

        return $query;
    }

    protected function filterableFields(): array
    {
        return [];
    }
}
