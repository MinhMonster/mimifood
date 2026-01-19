<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TopUpTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminTopUpNotification;

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

        $user = $request->user();

        $transaction = TopUpTransactions::create([
            'user_id'             => $user->id,
            'amount'              => $validated['amount'],
            'note'                => $validated['note'] ?? null,
            'status'              => 'pending',
            'transaction_at'      => now(),
        ]);

        try {
            Mail::to(config('mail.admin_email'))
                ->queue(new AdminTopUpNotification($user, $transaction));
        } catch (\Throwable $e) {
            Log::error('Send admin top-up mail failed', [
                'user_id'        => $user->id,
                'transaction_id' => $transaction->id,
                'amount'         => $transaction->amount,
                'error'          => $e->getMessage(),
            ]);
        }

        return fetchData($transaction);;
    }
}
