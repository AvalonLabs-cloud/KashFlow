<?php

namespace Twilio\Rest;

use Twilio\Rest\Conversations\V1;
use Twilio\Rest\Conversations\V1\AddressConfigurationContext;
use Twilio\Rest\Conversations\V1\AddressConfigurationList;
use Twilio\Rest\Conversations\V1\ConfigurationContext;
use Twilio\Rest\Conversations\V1\ConfigurationList;
use Twilio\Rest\Conversations\V1\ConversationContext;
use Twilio\Rest\Conversations\V1\ConversationList;
use Twilio\Rest\Conversations\V1\CredentialContext;
use Twilio\Rest\Conversations\V1\CredentialList;
use Twilio\Rest\Conversations\V1\ParticipantConversationList;
use Twilio\Rest\Conversations\V1\RoleContext;
use Twilio\Rest\Conversations\V1\RoleList;
use Twilio\Rest\Conversations\V1\ServiceContext;
use Twilio\Rest\Conversations\V1\ServiceList;
use Twilio\Rest\Conversations\V1\UserContext;
use Twilio\Rest\Conversations\V1\UserList;

class Conversations extends ConversationsBase
{
    /**
     * @deprecated Use v1->configuration instead.
     */
    protected function getConfiguration(): ConfigurationList
    {
        echo 'configuration is deprecated. Use v1->configuration instead.';

        return $this->v1->configuration;
    }

    /**
     * @deprecated Use v1->configuration() instead.
     */
    protected function contextConfiguration(): ConfigurationContext
    {
        echo 'configuration() is deprecated. Use v1->configuration() instead.';

        return $this->v1->configuration();
    }

    /**
     * @deprecated Use v1->addressConfigurations instead.
     */
    protected function getAddressConfigurations(): AddressConfigurationList
    {
        echo 'addressConfigurations is deprecated. Use v1->addressConfigurations instead.';

        return $this->v1->addressConfigurations;
    }

    /**
     * @deprecated Use v1->addressConfigurations(\$sid) instead.
     *
     * @param  string  $sid  The SID or Address of the Configuration.
     */
    protected function contextAddressConfigurations(string $sid): AddressConfigurationContext
    {
        echo 'addressConfigurations($sid) is deprecated. Use v1->addressConfigurations($sid) instead.';

        return $this->v1->addressConfigurations($sid);
    }

    /**
     * @deprecated Use v1->conversations instead.
     */
    protected function getConversations(): ConversationList
    {
        echo 'conversations is deprecated. Use v1->conversations instead.';

        return $this->v1->conversations;
    }

    /**
     * @deprecated Use v1->conversations(\$sid) instead.
     *
     * @param  string  $sid  A 34 character string that uniquely identifies this
     *                       resource.
     */
    protected function contextConversations(string $sid): ConversationContext
    {
        echo 'conversations($sid) is deprecated. Use v1->conversations($sid) instead.';

        return $this->v1->conversations($sid);
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
     * @deprecated Use v1->credentials(\$sid) instead.
     *
     * @param  string  $sid  A 34 character string that uniquely identifies this
     *                       resource.
     */
    protected function contextCredentials(string $sid): CredentialContext
    {
        echo 'credentials($sid) is deprecated. Use v1->credentials($sid) instead.';

        return $this->v1->credentials($sid);
    }

    /**
     * @deprecated Use v1->participantConversations instead.
     */
    protected function getParticipantConversations(): ParticipantConversationList
    {
        echo 'participantConversations is deprecated. Use v1->participantConversations instead.';

        return $this->v1->participantConversations;
    }

    /**
     * @deprecated Use v1->roles instead.
     */
    protected function getRoles(): RoleList
    {
        echo 'roles is deprecated. Use v1->roles instead.';

        return $this->v1->roles;
    }

    /**
     * @deprecated Use v1->roles(\$sid) instead.
     *
     * @param  string  $sid  The SID of the Role resource to fetch
     */
    protected function contextRoles(string $sid): RoleContext
    {
        echo 'roles($sid) is deprecated. Use v1->roles($sid) instead.';

        return $this->v1->roles($sid);
    }

    /**
     * @deprecated Use v1->services instead.
     */
    protected function getServices(): ServiceList
    {
        echo 'services is deprecated. Use v1->services instead.';

        return $this->v1->services;
    }

    /**
     * @deprecated Use v1->services(\$sid) instead.
     *
     * @param  string  $sid  A 34 character string that uniquely identifies this
     *                       resource.
     */
    protected function contextServices(string $sid): ServiceContext
    {
        echo 'services($sid) is deprecated. Use v1->services($sid) instead.';

        return $this->v1->services($sid);
    }

    /**
     * @deprecated Use v1->users instead.
     */
    protected function getUsers(): UserList
    {
        echo 'users is deprecated. Use v1->users instead.';

        return $this->v1->users;
    }

    /**
     * @deprecated Use v1->users(\$sid) instead.
     *
     * @param  string  $sid  The SID of the User resource to fetch
     */
    protected function contextUsers(string $sid): UserContext
    {
        echo 'users($sid) is deprecated. Use v1->users($sid) instead.';

        return $this->v1->users($sid);
    }
}
