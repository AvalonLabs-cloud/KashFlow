<?php
namespace App\Domains\Beneficiary\Services;
use App\Models\TransactionHistory;


class CheckIfToCreateBeneficiary{
    public function execute(TransactionHIstory $transactionHistory){
      $transactionHistoryDiretion = $transactionHistory->direction;
      if ($transactionHistoryDiretion === 'credit') {
         return false;
      }
      return true;
    }
}