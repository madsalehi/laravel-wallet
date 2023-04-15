<?php

namespace App\Services\WalletService;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class WalletService implements WalletRepository
{
    /**
     * @inheritDoc
     */
    public function increase(int $walletId, int $amount): bool
    {
        $wallet = $this->getWalletById($walletId);
        try {
            $transaction = DB::transaction(function () use ($wallet, $amount) {
                $wallet->balance += $amount;
                $wallet->save();

                Transaction::query()->create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user->id,
                    'port' => Transaction::PORT_DEPOSIT,
                    'amount' => $amount,
                ]);
            });

            return is_null($transaction);
        } catch (\Exception $e) {
            throw new WalletException(WalletException::TRANSACTION_FAILED);
        }
    }

    /**
     * @inheritDoc
     */
    public function decrease(int $walletId, int $amount): bool
    {
        $wallet = $this->getWalletById($walletId);
        if ($wallet->balance < $amount) {
            throw new WalletException(WalletException::INSUFFICIENT_BALANCE);
        }

        try {
            $transaction = DB::transaction(function () use ($wallet, $amount) {
                $wallet->balance -= $amount;
                $wallet->save();

                Transaction::query()->create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $wallet->user->id,
                    'port' => Transaction::PORT_WITHDRAW,
                    'amount' => $amount,
                ]);
            });

            return is_null($transaction);
        } catch (\Exception $e) {
            throw new WalletException(WalletException::TRANSACTION_FAILED);
        }
    }

    /**
     * @inheritDoc
     */
    public function getTransactions(int $walletId, int|null $perPage): LengthAwarePaginator
    {
        $wallet = $this->getWalletById($walletId);
        if (!is_null($perPage)) {
            return $wallet->transactions()->paginate($perPage);
        }

        return $wallet->transactions()->paginate();
    }

    /**
     * @inheritDoc
     */
    public function getBalance(int $walletId): int
    {
        $wallet = $this->getWalletById($walletId);
        return $wallet->balance;
    }

    /**
     * @inheritDoc
     */
    public function getWalletById(int $walletId): Wallet
    {
        $wallet = Wallet::query()->find($walletId);
        if (is_null($wallet)) {
            throw new WalletException(WalletException::WALLET_NOT_FOUND);
        }

        return $wallet;
    }
}
