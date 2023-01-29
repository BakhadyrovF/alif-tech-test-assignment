<?php

namespace App\Actions;

use App\DTOs\ContactEmailUpdateDTO;
use App\Models\ContactEmail;

final class ContactEmailUpdateAction
{
    /**
     * @param ContactEmail $contactEmail
     * @param ContactEmailUpdateDTO $dto
     * @return ContactEmail
     */
    public function handle(ContactEmail $contactEmail, ContactEmailUpdateDTO $dto): ContactEmail
    {
        $contactEmail->update([
            'email' => $dto->getEmail()
        ]);

        return $contactEmail;
    }
}
