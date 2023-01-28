<?php

namespace App\Http\Responses;

final class ResourceUpdatedResponse implements ResponseMessageContract
{

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return trans('Resource has been updated.');
    }
}
