<?php

namespace Twilio\Rest;

use Twilio\Rest\FlexApi\V1;
use Twilio\Rest\FlexApi\V1\AssessmentsContext;
use Twilio\Rest\FlexApi\V1\AssessmentsList;
use Twilio\Rest\FlexApi\V1\ChannelContext;
use Twilio\Rest\FlexApi\V1\ChannelList;
use Twilio\Rest\FlexApi\V1\ConfigurationContext;
use Twilio\Rest\FlexApi\V1\ConfigurationList;
use Twilio\Rest\FlexApi\V1\FlexFlowContext;
use Twilio\Rest\FlexApi\V1\FlexFlowList;
use Twilio\Rest\FlexApi\V1\InteractionContext;
use Twilio\Rest\FlexApi\V1\InteractionList;
use Twilio\Rest\FlexApi\V1\WebChannelContext;
use Twilio\Rest\FlexApi\V1\WebChannelList;
use Twilio\Rest\FlexApi\V2;
use Twilio\Rest\FlexApi\V2\WebChannelsList;

class FlexApi extends FlexApiBase
{
    /**
     * @deprecated Use v1->assessments instead.
     */
    protected function getAssessments(): AssessmentsList
    {
        echo 'assessments is deprecated. Use v1->assessments instead.';

        return $this->v1->assessments;
    }

    /**
     * @deprecated Use v1->assessments() instead.
     */
    protected function contextAssessments(): AssessmentsContext
    {
        echo 'assessments() is deprecated. Use v1->assessments() instead.';

        return $this->v1->assessments();
    }

    /**
     * @deprecated Use v1->channel instead.
     */
    protected function getChannel(): ChannelList
    {
        echo 'channel is deprecated. Use v1->channel instead.';

        return $this->v1->channel;
    }

    /**
     * @deprecated Use v1->channel(\$sid) instead.
     *
     * @param  string  $sid  The SID that identifies the Flex chat channel resource to
     *                       fetch
     */
    protected function contextChannel(string $sid): ChannelContext
    {
        echo 'channel($sid) is deprecated. Use v1->channel($sid) instead.';

        return $this->v1->channel($sid);
    }

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
     * @deprecated Use v1->flexFlow instead.
     */
    protected function getFlexFlow(): FlexFlowList
    {
        echo 'flexFlow is deprecated. Use v1->flexFlow instead.';

        return $this->v1->flexFlow;
    }

    /**
     * @deprecated Use v1->flexFlow(\$sid) instead.
     *
     * @param  string  $sid  The SID that identifies the resource to fetch
     */
    protected function contextFlexFlow(string $sid): FlexFlowContext
    {
        echo 'flexFlow($sid) is deprecated. Use v1->flexFlow($sid) instead.';

        return $this->v1->flexFlow($sid);
    }

    /**
     * @deprecated Use v1->interaction instead.
     */
    protected function getInteraction(): InteractionList
    {
        echo 'interaction is deprecated. Use v1->interaction instead.';

        return $this->v1->interaction;
    }

    /**
     * @deprecated Use v1->interaction(\$sid) instead.
     *
     * @param  string  $sid  The SID that identifies the resource to fetch
     */
    protected function contextInteraction(string $sid): InteractionContext
    {
        echo 'interaction($sid) is deprecated. Use v1->interaction($sid) instead.';

        return $this->v1->interaction($sid);
    }

    /**
     * @deprecated Use v1->webChannel instead.
     */
    protected function getWebChannel(): WebChannelList
    {
        echo 'webChannel is deprecated. Use v1->webChannel instead.';

        return $this->v1->webChannel;
    }

    /**
     * @deprecated Use v1->webChannel(\$sid) instead.
     *
     * @param  string  $sid  The SID of the WebChannel resource to fetch
     */
    protected function contextWebChannel(string $sid): WebChannelContext
    {
        echo 'webChannel($sid) is deprecated. Use v1->webChannel($sid) instead.';

        return $this->v1->webChannel($sid);
    }

    /**
     * @deprecated Use v2->webChannels instead.
     */
    protected function getWebChannels(): WebChannelsList
    {
        echo 'webChannels is deprecated. Use v2->webChannels instead.';

        return $this->v2->webChannels;
    }
}
