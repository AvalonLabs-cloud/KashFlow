<?php

namespace App\Contracts;

interface MustVerifyTransferTransactionEligibility
{
 /**
  *Contract implementation
  *
  *Ensure model implemets the contract that ensures that it can check for a users transaction ligibility
  *@return bool
  */
    public function verifyTransferTransactionEligability() : bool|array;
}

