<?php

namespace App;

enum TransactionReconsilationStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
}
