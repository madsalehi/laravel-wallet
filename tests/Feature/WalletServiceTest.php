<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use App\Services\WalletService\WalletException;
use App\Services\WalletService\WalletRepository;
use App\Services\WalletService\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use function PHPUnit\Framework\assertGreaterThan;
use function PHPUnit\Framework\assertLessThan;

class WalletServiceTest extends TestCase
{
    private WalletRepository $walletService;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->walletService = resolve(WalletService::class);
    }

    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_wallet_should_be_increased_after_deposit_transaction(): void
    {
        $user = User::query()->create([
            'name' => 'Test user',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        Wallet::query()->create([
            'user_id' => $user->id,
        ]);

        $wallet = $this->walletService->getWalletById(1);
        $initialBalance = $wallet->balance;

        $amount = 3100;

        $this->walletService->increase($wallet->id, $amount);

        assertGreaterThan($initialBalance, $amount);
    }

    public function test_wallet_should_be_decreased_after_withdraw_transaction(): void
    {
        $user = User::query()->create([
            'name' => 'Test user',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        Wallet::query()->create([
            'user_id' => $user->id,
        ]);

        $wallet = $this->walletService->getWalletById(1);
        $initialBalance = $wallet->balance;

        $amount = 20000;
        $this->walletService->increase($wallet->id, $amount);
        $this->walletService->decrease($wallet->id, $amount);

        assertLessThan($amount, $initialBalance);
    }

    public function test_wallet_on_insufficient_balance(): void
    {
        $user = User::query()->create([
            'name' => 'Test user',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        Wallet::query()->create([
            'user_id' => $user->id,
        ]);

        $wallet = $this->walletService->getWalletById(1);
        $initialBalance = $wallet->balance;

        $amount = 20000;

        $this->assertThrows(function () use ($amount, $wallet) {
            $this->walletService->decrease($wallet->id, $amount);
        }, WalletException::class);
    }

    public function test_wallet_throws_not_found_exception()
    {
        $user = User::query()->create([
            'name' => 'Test user',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        Wallet::query()->create([
            'user_id' => $user->id,
        ]);

        $this->assertThrows(function () {
            $this->walletService->getWalletById(2);
        }, WalletException::class);
    }
}
