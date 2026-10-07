<?php

namespace Twilio\Rest;

use Twilio\Rest\Autopilot\V1;
use Twilio\Rest\Autopilot\V1\AssistantContext;
use Twilio\Rest\Autopilot\V1\AssistantList;
use Twilio\Rest\Autopilot\V1\RestoreAssistantList;

class Autopilot extends AutopilotBase
{
    /**
     * @deprecated Use v1->assistants instead.
     */
    protected function getAssistants(): AssistantList
    {
        echo 'assistants is deprecated. Use v1->assistants instead.';

        return $this->v1->assistants;
    }

    /**
     * @deprecated Use v1->assistants(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextAssistants(string $sid): AssistantContext
    {
        echo 'assistants($sid) is deprecated. Use v1->assistants($sid) instead.';

        return $this->v1->assistants($sid);
    }

    /**
     * @deprecated Use v1->restoreAssistant instead
     */
    protected function getRestoreAssistant(): RestoreAssistantList
    {
        echo 'restoreAssistant is deprecated. Use v1->restoreAssistant instead.';

        return $this->v1->restoreAssistant;
    }
}
