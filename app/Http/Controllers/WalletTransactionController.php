<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = WalletTransaction::query()->search($request)->where('user_id', $request->user()->id);
        return formatPaginate($query, $request);
    }

    public function show(Request $request)
    {
        $transaction = WalletTransaction::where('id', $request->id)->where('user_id', $request->user()->id)->first();
        return response()->json($transaction);
    }
}
