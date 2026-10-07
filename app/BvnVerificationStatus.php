<?php

namespace App;

enum BvnVerificationStatus : string
{
    case NOT_VERIFIED = 'not_verified';
    case PENDING = 'pending';
    case VERIFIED = 'verified';     
}
    
