<?php

namespace App\Actions;

use App\DTOs\ContactEmailCreateDTO;
use App\Models\ContactEmail;

final class ContactEmailCreateAction
{
    /**
     * @param ContactEmailCreateDTO $dto
     * @return ContactEmail
     */
    public function handle(ContactEmailCreateDTO $dto): ContactEmail
    {
        return ContactEmail::create([
            'email' => $dto->getEmail(),
            'contact_id' => $dto->getContactId()
        ]);
    }
}
