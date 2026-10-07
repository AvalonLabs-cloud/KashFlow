<?php

namespace Twilio\Rest;

use Twilio\Rest\Api\V2010\Account\AddressContext;
use Twilio\Rest\Api\V2010\Account\AddressList;
use Twilio\Rest\Api\V2010\Account\ApplicationContext;
use Twilio\Rest\Api\V2010\Account\ApplicationList;
use Twilio\Rest\Api\V2010\Account\AuthorizedConnectAppContext;
use Twilio\Rest\Api\V2010\Account\AuthorizedConnectAppList;
use Twilio\Rest\Api\V2010\Account\AvailablePhoneNumberCountryContext;
use Twilio\Rest\Api\V2010\Account\AvailablePhoneNumberCountryList;
use Twilio\Rest\Api\V2010\Account\BalanceList;
use Twilio\Rest\Api\V2010\Account\CallContext;
use Twilio\Rest\Api\V2010\Account\CallList;
use Twilio\Rest\Api\V2010\Account\ConferenceContext;
use Twilio\Rest\Api\V2010\Account\ConferenceList;
use Twilio\Rest\Api\V2010\Account\ConnectAppContext;
use Twilio\Rest\Api\V2010\Account\ConnectAppList;
use Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberContext;
use Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberList;
use Twilio\Rest\Api\V2010\Account\KeyContext;
use Twilio\Rest\Api\V2010\Account\KeyList;
use Twilio\Rest\Api\V2010\Account\MessageContext;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Api\V2010\Account\NewKeyList;
use Twilio\Rest\Api\V2010\Account\NewSigningKeyList;
use Twilio\Rest\Api\V2010\Account\NotificationContext;
use Twilio\Rest\Api\V2010\Account\NotificationList;
use Twilio\Rest\Api\V2010\Account\OutgoingCallerIdContext;
use Twilio\Rest\Api\V2010\Account\OutgoingCallerIdList;
use Twilio\Rest\Api\V2010\Account\QueueContext;
use Twilio\Rest\Api\V2010\Account\QueueList;
use Twilio\Rest\Api\V2010\Account\RecordingContext;
use Twilio\Rest\Api\V2010\Account\RecordingList;
use Twilio\Rest\Api\V2010\Account\ShortCodeContext;
use Twilio\Rest\Api\V2010\Account\ShortCodeList;
use Twilio\Rest\Api\V2010\Account\SigningKeyContext;
use Twilio\Rest\Api\V2010\Account\SigningKeyList;
use Twilio\Rest\Api\V2010\Account\SipList;
use Twilio\Rest\Api\V2010\Account\TokenList;
use Twilio\Rest\Api\V2010\Account\TranscriptionContext;
use Twilio\Rest\Api\V2010\Account\TranscriptionList;
use Twilio\Rest\Api\V2010\Account\UsageList;
use Twilio\Rest\Api\V2010\Account\ValidationRequestList;
use Twilio\Rest\Api\V2010\AccountContext;
use Twilio\Rest\Api\V2010\AccountList;

class Api extends ApiBase
{
    /**
     * @return AccountContext Account provided as the
     *                        authenticating account
     */
    protected function getAccount(): AccountContext
    {
        return $this->v2010->account;
    }

    protected function getAccounts(): AccountList
    {
        return $this->v2010->accounts;
    }

    /**
     * @param  string  $sid  Fetch by unique Account Sid
     */
    protected function contextAccounts(string $sid): AccountContext
    {
        return $this->v2010->accounts($sid);
    }

    /**
     * @deprecated Use account->addresses instead.
     */
    protected function getAddresses(): AddressList
    {
        echo 'addresses is deprecated. Use account->addresses instead.';

        return $this->v2010->account->addresses;
    }

    /**
     * @deprecated Use account->addresses(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextAddresses(string $sid): AddressContext
    {
        echo 'addresses($sid) is deprecated. Use account->addresses($sid) instead.';

        return $this->v2010->account->addresses($sid);
    }

    /**
     * @deprecated Use account->applications instead.
     */
    protected function getApplications(): ApplicationList
    {
        echo 'applications is deprecated. Use account->applications instead.';

        return $this->v2010->account->applications;
    }

    /**
     * @deprecated Use account->applications(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextApplications(string $sid): ApplicationContext
    {
        echo 'applications($sid) is deprecated. Use account->applications($sid) instead.';

        return $this->v2010->account->applications($sid);
    }

    /**
     * @deprecated Use account->authorizedConnectApps instead.
     */
    protected function getAuthorizedConnectApps(): AuthorizedConnectAppList
    {
        echo 'authorizedConnectApps is deprecated. Use account->authorizedConnectApps instead.';

        return $this->v2010->account->authorizedConnectApps;
    }

    /**
     * @deprecated Use account->authorizedConnectApps(\$connectAppSid) instead.
     *
     * @param  string  $connectAppSid  The SID of the Connect App to fetch
     */
    protected function contextAuthorizedConnectApps(string $connectAppSid): AuthorizedConnectAppContext
    {
        echo 'authorizedConnectApps($connectAppSid) is deprecated. Use account->authorizedConnectApps($connectAppSid) instead.';

        return $this->v2010->account->authorizedConnectApps($connectAppSid);
    }

    /**
     * @deprecated Use account->availablePhoneNumbers instead.
     */
    protected function getAvailablePhoneNumbers(): AvailablePhoneNumberCountryList
    {
        echo 'availablePhoneNumbers is deprecated. Use account->availablePhoneNumbers instead.';

        return $this->v2010->account->availablePhoneNumbers;
    }

    /**
     * @deprecated Use account->availablePhoneNumbers(\$countryCode) instead.
     *
     * @param  string  $countryCode  The ISO country code of the country to fetch
     *                               available phone number information about
     */
    protected function contextAvailablePhoneNumbers(string $countryCode): AvailablePhoneNumberCountryContext
    {
        echo 'availablePhoneNumbers($countryCode) is deprecated. Use account->availablePhoneNumbers($countryCode) instead.';

        return $this->v2010->account->availablePhoneNumbers($countryCode);
    }

    /**
     * @deprecated Use account->balance instead.
     */
    protected function getBalance(): BalanceList
    {
        echo 'balance is deprecated. Use account->balance instead.';

        return $this->v2010->account->balance;
    }

    /**
     * @deprecated Use account->calls instead
     */
    protected function getCalls(): CallList
    {
        echo 'calls is deprecated. Use account->calls instead.';

        return $this->v2010->account->calls;
    }

    /**
     * @deprecated Use account->calls(\$sid) instead.
     *
     * @param  string  $sid  The SID of the Call resource to fetch
     */
    protected function contextCalls(string $sid): CallContext
    {
        echo 'calls($sid) is deprecated. Use account->calls($sid) instead.';

        return $this->v2010->account->calls($sid);
    }

    /**
     * @deprecated Use account->conferences instead.
     */
    protected function getConferences(): ConferenceList
    {
        echo 'conferences is deprecated. Use account->conferences instead.';

        return $this->v2010->account->conferences;
    }

    /**
     * @deprecated Use account->conferences(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies this resource
     */
    protected function contextConferences(string $sid): ConferenceContext
    {
        echo 'conferences($sid) is deprecated. Use account->conferences($sid) instead.';

        return $this->v2010->account->conferences($sid);
    }

    /**
     * @deprecated Use account->connectApps instead.
     */
    protected function getConnectApps(): ConnectAppList
    {
        echo 'connectApps is deprecated. Use account->connectApps instead.';

        return $this->v2010->account->connectApps;
    }

    /**
     * @deprecated account->connectApps(\$sid)
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextConnectApps(string $sid): ConnectAppContext
    {
        echo 'connectApps($sid) is deprecated. Use account->connectApps($sid) instead.';

        return $this->v2010->account->connectApps($sid);
    }

    /**
     * @deprecated Use account->incomingPhoneNumbers instead
     */
    protected function getIncomingPhoneNumbers(): IncomingPhoneNumberList
    {
        echo 'incomingPhoneNumbers is deprecated. Use account->incomingPhoneNumbers instead.';

        return $this->v2010->account->incomingPhoneNumbers;
    }

    /**
     * @deprecated Use account->incomingPhoneNumbers(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextIncomingPhoneNumbers(string $sid): IncomingPhoneNumberContext
    {
        echo 'incomingPhoneNumbers($sid) is deprecated. Use account->incomingPhoneNumbers($sid) instead.';

        return $this->v2010->account->incomingPhoneNumbers($sid);
    }

    /**
     * @deprecated Use account->keys instead.
     */
    protected function getKeys(): KeyList
    {
        echo 'keys is deprecated. Use account->keys instead.';

        return $this->v2010->account->keys;
    }

    /**
     * @deprecated Use account->keys(\$sid) instead
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextKeys(string $sid): KeyContext
    {
        echo 'keys($sid) is deprecated. Use account->keys($sid) instead.';

        return $this->v2010->account->keys($sid);
    }

    /**
     * @deprecated Use account->messages instead.
     */
    protected function getMessages(): MessageList
    {
        echo 'messages is deprecated. Use account->messages instead.';

        return $this->v2010->account->messages;
    }

    /**
     * @deprecated Use account->messages(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextMessages(string $sid): MessageContext
    {
        echo 'amessages($sid) is deprecated. Use account->messages($sid) instead.';

        return $this->v2010->account->messages($sid);
    }

    /**
     * @deprecated Use account->newKeys instead.
     */
    protected function getNewKeys(): NewKeyList
    {
        echo 'newKeys is deprecated. Use account->newKeys instead.';

        return $this->v2010->account->newKeys;
    }

    /**
     * @deprecated Use account->newSigningKeys instead.
     */
    protected function getNewSigningKeys(): NewSigningKeyList
    {
        echo 'newSigningKeys is deprecated. Use account->newSigningKeys instead.';

        return $this->v2010->account->newSigningKeys;
    }

    /**
     * @deprecated Use account->notifications instead.
     */
    protected function getNotifications(): NotificationList
    {
        echo 'notifications is deprecated. Use account->notifications instead.';

        return $this->v2010->account->notifications;
    }

    /**
     * @deprecated Use account->notifications(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextNotifications(string $sid): NotificationContext
    {
        echo 'notifications($sid) is deprecated. Use account->notifications($sid) instead.';

        return $this->v2010->account->notifications($sid);
    }

    /**
     * @deprecated Use account->outgoingCallerIds instead.
     */
    protected function getOutgoingCallerIds(): OutgoingCallerIdList
    {
        echo 'outgoingCallerIds is deprecated. Use account->outgoingCallerIds instead.';

        return $this->v2010->account->outgoingCallerIds;
    }

    /**
     * @deprecated Use account->outgoingCallerIds(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextOutgoingCallerIds(string $sid): OutgoingCallerIdContext
    {
        echo 'outgoingCallerIds($sid) is deprecated. Use account->outgoingCallerIds($sid) instead.';

        return $this->v2010->account->outgoingCallerIds($sid);
    }

    /**
     * @deprecated Use account->queues instead.
     */
    protected function getQueues(): QueueList
    {
        echo 'queues is deprecated. Use account->queues instead.';

        return $this->v2010->account->queues;
    }

    /**
     * @deprecated Use account->queues(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies this resource
     */
    protected function contextQueues(string $sid): QueueContext
    {
        echo 'queues($sid) is deprecated. Use account->queues($sid) instead.';

        return $this->v2010->account->queues($sid);
    }

    /**
     * @deprecated Use account->recordings instead.
     */
    protected function getRecordings(): RecordingList
    {
        echo 'recordings is deprecated. Use account->recordings instead.';

        return $this->v2010->account->recordings;
    }

    /**
     * @deprecated Use account->recordings(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextRecordings(string $sid): RecordingContext
    {
        echo 'recordings($sid) is deprecated. Use account->recordings($sid) instead.';

        return $this->v2010->account->recordings($sid);
    }

    /**
     * @deprecated  Use account->signingKeys instead.
     */
    protected function getSigningKeys(): SigningKeyList
    {
        echo 'signingKeys is deprecated. Use account->signingKeys instead.';

        return $this->v2010->account->signingKeys;
    }

    /**
     * @deprecated Use account->signingKeys(\$sid) instead.
     *
     * @param  string  $sid  The sid
     */
    protected function contextSigningKeys(string $sid): SigningKeyContext
    {
        echo 'signingKeys($sid) is deprecated. Use account->signingKeys($sid) instead.';

        return $this->v2010->account->signingKeys($sid);
    }

    /**
     * @deprecated Use account->sip instead.
     */
    protected function getSip(): SipList
    {
        echo 'sip is deprecated. Use account->sip instead.';

        return $this->v2010->account->sip;
    }

    /**
     * @deprecated Use account->shortCodes instead.
     */
    protected function getShortCodes(): ShortCodeList
    {
        echo 'shortCodes is deprecated. Use account->shortCodes instead.';

        return $this->v2010->account->shortCodes;
    }

    /**
     * @deprecated Use account->shortCodes(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies this resource
     */
    protected function contextShortCodes(string $sid): ShortCodeContext
    {
        echo 'shortCodes($sid) is deprecated. Use account->shortCodes($sid) instead.';

        return $this->v2010->account->shortCodes($sid);
    }

    /**
     * @deprecated Use account->token instead.
     */
    protected function getTokens(): TokenList
    {
        echo 'tokens is deprecated. Use account->token instead.';

        return $this->v2010->account->tokens;
    }

    /**
     * @deprecated Use account->transcriptions instead.
     */
    protected function getTranscriptions(): TranscriptionList
    {
        echo 'transcriptions is deprecated. Use account->transcriptions instead.';

        return $this->v2010->account->transcriptions;
    }

    /**
     * @deprecated Use account->transcriptions(\$sid) instead
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextTranscriptions(string $sid): TranscriptionContext
    {
        echo 'transcriptions($sid) is deprecated. Use account->transcriptions($sid) instead.';

        return $this->v2010->account->transcriptions($sid);
    }

    /**
     * @deprecated Use account->usage instead.
     */
    protected function getUsage(): UsageList
    {
        echo 'usage is deprecated. Use account->usage instead.';

        return $this->v2010->account->usage;
    }

    /**
     * @deprecated Use account->validationRequests instead.
     */
    protected function getValidationRequests(): ValidationRequestList
    {
        echo 'validationRequests is deprecated. Use account->validationRequests instead.';

        return $this->v2010->account->validationRequests;
    }
}
