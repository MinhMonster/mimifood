<?php

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

if (!function_exists('formatPaginate')) {
    function formatPaginate($query, $request)
    {
        $input = json_decode($request['input'] ?? '{}');
        $pagination = $query
            ->orderBy('id', 'desc')
            ->paginate($input->perPage ?? 30, ['*'], 'page', $input->page ?? 1);
        $response = [
            'response' => [
                'count' => $pagination->total(),
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
            'message' => 'Successs'
        ];
        return response()->json($response);
    }
}

if (!function_exists('fetchData')) {
    function fetchData($response)
    {
        $response = [
            'response' => $response,
            'message' => 'Successs'
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
