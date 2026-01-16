<?php

namespace App\Filters;

use Illuminate\Http\Request;

class DragonBallFilter
{
    public static function apply($query, Request $request)
    {
        return $query
            ->when($request->code, fn ($q) =>
                $q->where('code', 'like', "%{$request->code}%")
            )
            ->when($request->server, fn ($q) =>
                $q->where('server', $request->server)
            )
            ->when($request->planet, fn ($q) =>
                $q->where('planet', $request->planet)
            )
            ->when($request->price_from, fn ($q) =>
                $q->where('selling_price', '>=', $request->price_from)
            )
            ->when($request->price_to, fn ($q) =>
                $q->where('selling_price', '<=', $request->price_to)
            );
    }
}
