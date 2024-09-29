<?php

if (!function_exists('format_date')) {
    function format_date($date, $format = 'Y-m-d H:i:s') {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('is_active')) {
    function is_active($routeName) {
        return request()->routeIs($routeName) ? 'active' : '';
    }
}

if (!function_exists('formatPaginate')) {
    function formatPaginate($response, $request) {
        $input = json_decode($request['input']);
        $response = collect($response->paginate($input->perPage ?? 30));
        $response = [
            'response' => [
                'count' => $response['total'],
                'data' => $response['data'],
                'meta' => [
                    'count' => $response['total'],
                    'page' => $response['current_page'],
                    'pages'=> $response['last_page'],
                    'per_page'=> $response['per_page'],
                    'from' => $response['from'],
                    'to'=> $response['to'],
                ]
            ]
        ];
        return response()->json($response);
    }
}

if (!function_exists('fetchData')) {
    function fetchData($response) {
        $response = [
            'response' => $response
        ];

        return response()->json($response);
    }
}
