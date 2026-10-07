<?php

namespace Twilio\Rest;

use Twilio\Rest\Chat\V2;
use Twilio\Rest\Chat\V2\CredentialContext;
use Twilio\Rest\Chat\V2\CredentialList;
use Twilio\Rest\Chat\V2\ServiceContext;
use Twilio\Rest\Chat\V2\ServiceList;
use Twilio\Rest\Chat\V3;
use Twilio\Rest\Chat\V3\ChannelContext;
use Twilio\Rest\Chat\V3\ChannelList;

class Chat extends ChatBase
{
    /**
     * @deprecated Use v2->credentials instead.
     */
    protected function getCredentials(): CredentialList
    {
        echo 'credentials is deprecated. Use v2->credentials instead.';

        return $this->v2->credentials;
    }

    /**
     * @deprecated Use v2->credentials(\$sid) instead.
     *
     * @param  string  $sid  The SID of the Credential resource to fetch
     */
    protected function contextCredentials(string $sid): CredentialContext
    {
        echo 'credentials($sid) is deprecated. Use v2->credentials($sid) instead.';

        return $this->v2->credentials($sid);
    }

    /**
     * @deprecated Use v2->services instead.
     */
    protected function getServices(): ServiceList
    {
        echo 'services is deprecated. Use v2->services instead.';

        return $this->v2->services;
    }

    /**
     * @deprecated Use v2->services(\$sid) instead.
     *
     * @param  string  $sid  The SID of the Service resource to fetch
     */
    protected function contextServices(string $sid): ServiceContext
    {
        echo 'services($sid) is deprecated. Use v2->services($sid) instead.';

        return $this->v2->services($sid);
    }

    /**
     * @deprecated Use v3->channels instead.
     */
    protected function getChannels(): ChannelList
    {
        echo 'channels is deprecated. Use v3->channels instead.';

        return $this->v3->channels;
    }

    /**
     * @deprecated Use v3->channels(\$serviceSid, \$sid) instead.
     *
     * @param  string  $serviceSid  Service Sid.
     * @param  string  $sid  A string that uniquely identifies this Channel.
     */
    protected function contextChannels(string $serviceSid, string $sid): ChannelContext
    {
        echo 'channels($serviceSid, $sid) is deprecated. Use v3->channels($serviceSid, $sid) instead.';

        return $this->v3->channels($serviceSid, $sid);
    }
}
