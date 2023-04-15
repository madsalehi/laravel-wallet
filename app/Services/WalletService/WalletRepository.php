<?php

namespace App\Services\WalletService;

use App\Models\Wallet;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

interface WalletRepository
{
    /**
     * @param int $walletId
     * @return Wallet
     * @throws WalletException
     */
    public function getWalletById(int $walletId): Wallet;

    /**
     * @param int $walletId
     * @param int $amount
     * @return bool
     * @throws Throwable
     */
    public function increase(int $walletId, int $amount): bool;

    /**
     * @param int $walletId
     * @param int $amount
     * @return bool
     * @throws WalletException
     * @throws Throwable
     */
    public function decrease(int $walletId, int $amount): bool;

    /**
     * @param int $walletId
     */
    public function getTransactions(int $walletId, int|null $perPage):LengthAwarePaginator;

    /**
     * @param int $walletId
     * @throws Throwable
     * @return int
     */
    public function getBalance(int $walletId): int;
}
