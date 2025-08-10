<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\WalletTransaction;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = WalletTransaction::query()->search($request)->with('user');

        return formatPaginate($query, $request);
    }

    public function show($id)
    {
        $transaction = WalletTransaction::with('user')->findOrFail($id);
        return response()->json($transaction);
    }

    public function destroy($id)
    {
        $transaction = WalletTransaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
