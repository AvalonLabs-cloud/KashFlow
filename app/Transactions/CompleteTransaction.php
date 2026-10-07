<?php
namespace App\Transactions;
use App\Models\Transaction;
use App\Models\HeldBalance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\TransactionStatus;
use App\Domains\Wallet\Repository;
use App\Events\BalanceUpdated;

class CompleteTransaction
{
     public function __construct(protected Repository $repository){

     }
    public function excecute(string $reference , User $user , Transaction $transaction , HeldBalance $heldBalance): Transaction
    {
        return DB::transaction(function () use ($reference , $transaction , $user , $heldBalance ) {
            $user = $transaction->user()->get();

            $transaction = $transaction->where('reference' , $reference)->lockForUpdate();

            if ($transaction->status === TransactionStatus::SUCCESSFUL) {
                return $transaction;
            }

            $transaction->update([
                'status' => TransactionStatus::SUCCESSFUL,
                'completed_at' => now(),
            ]);
            $user->heldBalances->where('amount' , $transaction->amount)->update('status' , 'completed');
            $beneficiary = $user->beneficiaries()->where('bank_name' , $transaction->bank_name)->where('bank_account_number' , $transaction->bank_account_number)->exists();
            if(!$beneficiary){
                 $user->beneficiaries()->create([
                    'bank_name' => $transaction->bank_name,
                    'bank_account_number' => $transaction->bank_account_number,
                    'account_name' => 'john doe',
                 ]);
            };
            $balance = $this->repository->AvailableBalance();

            BalanceUpdated::dispatch(user: $user , balance: $balance);

            return $transaction;
        });
    }
}
