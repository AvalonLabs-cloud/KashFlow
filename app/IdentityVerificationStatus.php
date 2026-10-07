<?php

namespace App;

enum IdentityVerificationStatus : string
{
    case NOT_VERIFIED = 'not_verified';
    case PENDING = 'pending';
    case VERIFIED = 'verified';
}
