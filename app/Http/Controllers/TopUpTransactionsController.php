<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TopUpTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TopUpTransactionsController extends Controller
{
    public function index(Request $request)
    {
        $query = TopUpTransactions::query()->search($request)->where('user_id', $request->user()->id);

        return formatPaginate($query, $request);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'              => 'required|numeric|min:10000|max:100000000',
            'note'                => 'nullable|string|max:1000',
        ]);

        $transaction = TopUpTransactions::create([
            'user_id'             => $request->user()->id,
            'amount'              => $validated['amount'],
            'note'                => $validated['note'] ?? null,
            'status'              => 'pending',
            'transaction_at'      => now(),
        ]);

        return fetchData($transaction);;
    }
}
