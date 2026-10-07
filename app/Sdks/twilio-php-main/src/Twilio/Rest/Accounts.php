<?php

namespace Twilio\Rest;

use Twilio\Rest\Accounts\V1;
use Twilio\Rest\Accounts\V1\AuthTokenPromotionContext;
use Twilio\Rest\Accounts\V1\AuthTokenPromotionList;
use Twilio\Rest\Accounts\V1\CredentialList;
use Twilio\Rest\Accounts\V1\SecondaryAuthTokenContext;
use Twilio\Rest\Accounts\V1\SecondaryAuthTokenList;

class Accounts extends AccountsBase
{
    /**
     * @deprecated Use v1->authTokenPromotion instead
     */
    protected function getAuthTokenPromotion(): AuthTokenPromotionList
    {
        echo 'authTokenPromotion is deprecated. Use v1->authTokenPromotion instead.';

        return $this->v1->authTokenPromotion;
    }

    /**
     * @deprecated Use v1->authTokenPromotion() instead.
     */
    protected function contextAuthTokenPromotion(): AuthTokenPromotionContext
    {
        echo 'authTokenPromotion() is deprecated. Use v1->authTokenPromotion() instead.';

        return $this->v1->authTokenPromotion();
    }

    /**
     * @deprecated Use v1->credentials instead.
     */
    protected function getCredentials(): CredentialList
    {
        echo 'credentials is deprecated. Use v1->credentials instead.';

        return $this->v1->credentials;
    }

    /**
     * @deprecated Use v1->secondaryAuthToken instead.
     */
    protected function getSecondaryAuthToken(): SecondaryAuthTokenList
    {
        echo 'secondaryAuthToken is deprecated. Use v1->secondaryAuthToken instead.';

        return $this->v1->secondaryAuthToken;
    }

    /**
     * @deprecated Use v1->secondaryAuthToken() instead.
     */
    protected function contextSecondaryAuthToken(): SecondaryAuthTokenContext
    {
        echo 'secondaryAuthToken() is deprecated. Use v1->secondaryAuthToken() instead.';

        return $this->v1->secondaryAuthToken();
    }
}
