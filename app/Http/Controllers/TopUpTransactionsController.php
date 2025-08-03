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
            'bank_name'           => 'required|string|max:255',
            'account_number'      => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
            'amount'              => 'required|numeric|min:10000',
            'note'                => 'nullable|string|max:1000',
        ]);

        $transaction = TopUpTransactions::create([
            'user_id'             => $request->user()->id,
            'bank_name'           => $validated['bank_name'],
            'account_number'      => $validated['account_number'],
            'account_holder_name' => $validated['account_holder_name'],
            'amount'              => $validated['amount'],
            'note'                => $validated['note'] ?? null,
            'status'              => 'pending',
            'transaction_at'      => now(),
        ]);

        return fetchData($transaction);;
    }
}
