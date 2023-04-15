<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransactionListRequest;
use App\Http\Requests\WithdrawRequest;
use App\Http\Resources\TransactionResource;
use App\Services\WalletService\WalletRepository;
use App\Services\WalletService\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    private WalletRepository $walletService;

    public function __construct()
    {
        $this->walletService = resolve(WalletService::class);
    }

    public function getWalletBalance()
    {
        return response()->json([
            'data' => [
                'balance' => $this->walletService->getBalance(1)
            ]
        ]);
    }

    public function deposit(DepositRequest $request)
    {
        $amount = $request->input('amount');

        if (!$this->walletService->increase(1, $amount)) {
            return response()->json([
                'message' => 'Deposit failed'
            ], 500);
        }

        return response()->json([
            'message' => 'Deposit successfully done'
        ]);
    }

    public function withdraw(WithdrawRequest $request)
    {
        $amount = $request->input('amount');

        if (!$this->walletService->decrease(1, $amount)) {
            return response()->json([
                'message' => 'Withdraw failed'
            ], 500);
        }

        return response()->json([
            'message' => 'Withdraw successfully done'
        ]);
    }

    public function getTransactions(TransactionListRequest $request)
    {
        $perPage = $request->input('per_page');
        return TransactionResource::collection($this->walletService->getTransactions(1, (int)$perPage));
    }
}
