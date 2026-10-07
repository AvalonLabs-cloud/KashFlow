<?php
namespace App\Domains\Wallet;
use App\Models\User;
use App\TransactionStatus;

class Repository{
  protected User $user;
   public function __construct()
   {
      $this->user = auth('web')->user();
   }

   public function AvailableBalance(){
       $userTransactions= $this->user->transactions()->get();
       $completedTransactions = $userTransactions->where('status' ,TransactionStatus::class);
       $creditTransactions = $completedTransactions->where('type' , 'credit');
       $debitTransactions = $completedTransactions->where('type' , 'debit');
       $heldBalance = $this->user->heldBalances()->where('status' ,'released');
       $availableBalance = $creditTransactions->sum('amount') - $debitTransactions->sum('amount') - $heldBalance->sum('amount');
       return $availableBalance;
   }

}

