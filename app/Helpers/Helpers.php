<?php
use Illuminate\Database\Eloquent\Builder;

if (!function_exists('format_date')) {
    function format_date($date, $format = 'Y-m-d H:i:s')
    {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('is_active')) {
    function is_active($routeName)
    {
        return request()->routeIs($routeName) ? 'active' : '';
    }
}

if (!function_exists('calculateSumsFromQuery')) {
    function calculateSumsFromQuery($query, array $sumColumns): array
    {
        $sums = [];

        foreach ($sumColumns as $key => $config) {

            /* =========================
             | CASE 1: ['purchase_price']
             ========================= */
            if (is_int($key)) {
                $column = $config;

                if (!is_string($column)) continue;
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) continue;

                $sums[$column] = (int) (clone $query)->sum($column);
                continue;
            }

            /* =========================
             | CASE 2: ['selling_price' => 0.1]
             ========================= */
            if (is_numeric($config)) {
                $column = $key;
                $rate   = $config;

                if (!is_string($column)) continue;
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) continue;

                $sum = (clone $query)->sum($column);
                $sums[$column] = (int) round($sum * $rate);
                continue;
            }

            /* =========================
             | CASE 3: công thức (PLUS - MINUS)
             ========================= */
            if (is_array($config)) {
                $result = 0;

                // PLUS: ['col' => rate]
                foreach ($config['plus'] ?? [] as $col => $rate) {
                    if (!is_string($col)) continue;
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) continue;

                    $sum = (clone $query)->sum($col);
                    $result += $sum * ($rate ?? 1);
                }

                // MINUS: ['col1', 'col2']
                foreach ($config['minus'] ?? [] as $col) {
                    if (!is_string($col)) continue;
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) continue;

                    $sum = (clone $query)->sum($col);
                    $result -= $sum;
                }

                $sums[$key] = (int) round($result);
            }
        }

        return $sums;
    }
}


if (!function_exists('formatPaginate')) {
    function formatPaginate(
        $query,
        $request,
        array $hidden = [],
        array $sumColumns = []
    ) {
        if (isset($request['input'])) {
            $input = json_decode($request['input'] ?? '{}');
        } else {
            $input = $request;
        }

        $pagination = (clone $query)
            ->orderBy('id', 'desc')
            ->paginate(
                $input->perPage ?? 30,
                ['*'],
                'page',
                $input->page ?? 1
            );

        if (!empty($hidden)) {
            $pagination->getCollection()->each->makeHidden($hidden);
        }

        $sums = [];

        if (!empty($sumColumns)) {
            $sums = calculateSumsFromQuery($query, $sumColumns);
        }

        return response()->json([
            'response' => [
                'count' => $pagination->total(),
                'sums' => $sums,
                'data' => $pagination->items(),
                'meta' => [
                    'count' => $pagination->total(),
                    'page' => $pagination->currentPage(),
                    'pages' => $pagination->lastPage(),
                    'per_page' => $pagination->perPage(),
                    'from' => $pagination->firstItem(),
                    'to' => $pagination->lastItem(),
                ]
            ],
            'message' => 'Success'
        ]);
    }
}

if (!function_exists('fetchData')) {
    function fetchData($response, $message = 'Successs')
    {
        $response = [
            'response' => $response,
            'message' => $message
        ];

        return response()->json($response);
    }
}

if (!function_exists('calculatePercentFromTiers')) {
    function calculatePercentFromTiers($priceTiers, $price): int
    {
        if (empty($priceTiers)) return 0;
        foreach ($priceTiers as $tier) {
            if ($price <= $tier['price']) {
                return $tier['value'];
            }
        }
        return end($priceTiers)['value'] ?? 0;
    }
}

if (! function_exists('buildFolderPath')) {
    /**
     * @param string|null $parentPath
     * @param string $folderName
     * @return string
     */
    function buildFolderPath(?string $parentPath, string $folderName = ''): string
    {
        $parentPath = $parentPath ?? config('filesystems.default_folder');
        $parentPath = trim($parentPath, '/') . '/';
        $folderName = trim($folderName, '/');
        $fullPath = $folderName !== '' ? $parentPath . $folderName . '/' : $parentPath;
        $fullPath = preg_replace('#/+#', '/', $fullPath);

        return $fullPath;
    }
}

if (!function_exists('apply_range_filter')) {
    /**
     * Apply range filter from string value: "min-max" | "min"
     *
     * @param  Builder $query
     * @param  string  $column
     * @param  mixed   $value
     * @return void
     */
    function apply_range_filter(Builder $query, string $column, $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        // "min-max"
        if (is_string($value) && str_contains($value, '-')) {
            [$min, $max] = array_pad(
                array_map('intval', explode('-', $value, 2)),
                2,
                null
            );

            if ($min !== null && $max !== null) {
                $query->whereBetween($column, [$min, $max]);
            } elseif ($min !== null) {
                $query->where($column, '>=', $min);
            } elseif ($max !== null) {
                $query->where($column, '<=', $max);
            }

            return;
        }

        // "min"  (>= min)
        if (is_numeric($value)) {
            $query->where($column, '>=', (int) $value);
        }
    }
}
