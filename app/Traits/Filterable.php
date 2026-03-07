<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

trait Filterable
{

    protected function normalizeFilters(array $filters): array
    {
        $result = [];

        foreach ($filters as $key => $value) {

            // 1.dot column.from, column.to
            if (str_contains($key, '.')) {
                Arr::set($result, $key, $value);
                continue;
            }

            // 2.suffix: column_from, column_to
            if (preg_match('/(.+)_(from|to)$/', $key, $matches)) {
                $field = $matches[1];
                $type  = $matches[2];

                $result[$field][$type] = $value;
                continue;
            }

            // 3. default
            $result[$key] = $value;
        }

        return $result;
    }

    protected function applyFilter($query, $column, $operator, $value)
    {
        switch ($operator) {

            case 'like':
                $query->where($column, 'like', "%{$value}%");
                break;

            case 'in':
                $query->whereIn($column, (array) $value);
                break;

            case 'range':
                apply_range_filter($query, $column, $value);
                break;
            case 'date_range':
                $from = $value['from'] ?? null;
                $to   = $value['to'] ?? null;

                if ($from) {
                    $query->whereDate($column, '>=', $from);
                }

                if ($to) {
                    $query->whereDate($column, '<=', $to);
                }
                break;
            default:
                $query->where($column, $operator, $value);
                break;
        }
    }

    public function scopeFilter(Builder $query, ?array $filters = null)
    {
        $filters = $filters ?? request()->all();
        $filters = $this->normalizeFilters($filters);

        foreach ($this->filterableFields() as $key => $config) {
            if (
                !array_key_exists($key, $filters) ||
                $filters[$key] === null ||
                $filters[$key] === ''
            ) {
                continue;
            }

            $value = $filters[$key];

            // Custom filter
            if (is_callable($config)) {
                $config($query, $value, $filters);
                continue;
            }

            $column   = $config[0];
            $operator = $config[1] ?? '=';

            // 🔥 relation
            if (str_contains($column, '.')) {
                $parts = explode('.', $column);
                $relColumn = array_pop($parts);
                $relation  = implode('.', $parts);

                $query->whereHas($relation, function ($q) use ($relColumn, $operator, $value) {
                    $this->applyFilter($q, $relColumn, $operator, $value);
                });

                continue;
            }

            // 🔽 normal
            $this->applyFilter($query, $column, $operator, $value);
        }

        return $query;
    }

    protected function filterableFields(): array
    {
        return [];
    }
}
