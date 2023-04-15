<?php

namespace App\Services\WalletService;

use App\Exceptions\ServiceException;
use Symfony\Component\HttpFoundation\Response;

class WalletException extends ServiceException
{
    public const WALLET_NOT_FOUND = 'wallet_not_found';
    public const INSUFFICIENT_BALANCE = 'insufficient_balance';
    public const TRANSACTION_FAILED = 'transaction_failed';

    public function configureExceptions()
    {
        $this->addException(self::WALLET_NOT_FOUND, Response::HTTP_NOT_FOUND, 'wallet not found');
        $this->addException(self::INSUFFICIENT_BALANCE, Response::HTTP_BAD_REQUEST, 'insufficient balance');
        $this->addException(self::TRANSACTION_FAILED, Response::HTTP_INTERNAL_SERVER_ERROR, 'error while inserting data');
    }
}
