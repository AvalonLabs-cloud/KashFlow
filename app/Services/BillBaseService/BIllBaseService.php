<?php

namespace App\Services\BillBaseService;

use App\Contracts\BIllTransaction as ContractsBIllTransaction;
use App\Traits\BillBaseServiceTrait\BillBaseServiceTrait;

class BIllBaseService implements ContractsBIllTransaction
{
    use BillBaseServiceTrait;
}
