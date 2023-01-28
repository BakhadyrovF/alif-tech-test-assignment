<?php

namespace App\Http\Responses;

final class ResourceCreatedResponse implements ResponseMessageContract
{
    /**
     * @return string
     */
    public function getMessage(): string
    {
        return trans('Resource has been created.');
    }
}
