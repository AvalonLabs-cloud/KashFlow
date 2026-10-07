<?php

namespace Twilio\Rest;

use Twilio\Rest\Events\V1;
use Twilio\Rest\Events\V1\EventTypeContext;
use Twilio\Rest\Events\V1\EventTypeList;
use Twilio\Rest\Events\V1\SchemaContext;
use Twilio\Rest\Events\V1\SchemaList;
use Twilio\Rest\Events\V1\SinkContext;
use Twilio\Rest\Events\V1\SinkList;
use Twilio\Rest\Events\V1\SubscriptionContext;
use Twilio\Rest\Events\V1\SubscriptionList;

class Events extends EventsBase
{
    /**
     * @deprecated Use v1->eventTypes instead.
     */
    protected function getEventTypes(): EventTypeList
    {
        echo 'eventTypes is deprecated. Use v1->eventTypes instead.';

        return $this->v1->eventTypes;
    }

    /**
     * @deprecated Use v1->eventTypes(\$type) instead.
     *
     * @param  string  $type  A string that uniquely identifies this Event Type.
     */
    protected function contextEventTypes(string $type): EventTypeContext
    {
        echo 'eventTypes($type) is deprecated. Use v1->eventTypes($type) instead.';

        return $this->v1->eventTypes($type);
    }

    /**
     * @deprecated Use v1->schemas instead.
     */
    protected function getSchemas(): SchemaList
    {
        echo 'schemas is deprecated. Use v1->schemas instead.';

        return $this->v1->schemas;
    }

    /**
     * @deprecated Use v1->schemas(\$id) instead.
     *
     * @param  string  $id  The unique identifier of the schema.
     */
    protected function contextSchemas(string $id): SchemaContext
    {
        echo 'schemas($id) is deprecated. Use v1->schemas($id) instead.';

        return $this->v1->schemas($id);
    }

    /**
     * @deprecated Use v1->sinks instead.
     */
    protected function getSinks(): SinkList
    {
        echo 'sinks is deprecated. Use v1->sinks instead.';

        return $this->v1->sinks;
    }

    /**
     * @deprecated Use v1->sinks(\$sid) instead
     *
     * @param  string  $sid  A string that uniquely identifies this Sink.
     */
    protected function contextSinks(string $sid): SinkContext
    {
        echo 'sinks($sid) is deprecated. Use v1->sinks($sid) instead.';

        return $this->v1->sinks($sid);
    }

    /**
     * @deprecated Use v1->subscriptions instead.
     */
    protected function getSubscriptions(): SubscriptionList
    {
        echo 'subscriptions is deprecated. Use v1->subscriptions instead.';

        return $this->v1->subscriptions;
    }

    /**
     * @deprecated Use v1->subscriptions(\$sid) instead.
     *
     * @param  string  $sid  A string that uniquely identifies this Subscription.
     */
    protected function contextSubscriptions(string $sid): SubscriptionContext
    {
        echo 'subscriptions($sid) is deprecated. Use v1->subscriptions($sid) instead.';

        return $this->v1->subscriptions($sid);
    }
}
