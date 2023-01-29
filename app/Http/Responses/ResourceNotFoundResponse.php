<?php

namespace App\Http\Responses;

final class ResourceNotFoundResponse implements ResponseMessageContract
{
    /**
     * @return string
     */
    public function getMessage(): string
    {
        return trans('Resource with provided Id not found.');
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return 404;
    }
}
