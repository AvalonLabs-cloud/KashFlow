<?php

namespace Twilio\Rest;

use Twilio\Rest\Content\V1;
use Twilio\Rest\Content\V1\ContentContext;
use Twilio\Rest\Content\V1\ContentList;

class Content extends ContentBase
{
    /**
     * @deprecated Use v1->contents instead.
     */
    protected function getContents(): ContentList
    {
        echo 'contents is deprecated. Use v1->contents instead.';

        return $this->v1->contents;
    }

    /**
     * @deprecated Use v1->contents(\$sid) instead.
     *
     * @param  string  $sid  The unique string that identifies the resource
     */
    protected function contextContents(string $sid): ContentContext
    {
        echo 'contents($sid) is deprecated. Use v1->contents($sid) instead.';

        return $this->v1->contents($sid);
    }
}
