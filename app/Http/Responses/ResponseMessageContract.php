<?php

namespace App\Http\Responses;

interface ResponseMessageContract
{
    /**
     * @return string
     */
    public function getMessage(): string;
}
