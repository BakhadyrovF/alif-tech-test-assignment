<?php

namespace App\Actions;

use App\DTOs\ContactUpdateDTO;
use App\Models\Contact;

final class ContactUpdateAction
{
    /**
     * @param Contact $contact
     * @param ContactUpdateDTO $dto
     * @return Contact
     */
    public function handle(Contact $contact, ContactUpdateDTO $dto): Contact
    {
        $contact->update([
            'name' => $dto->getName()
        ]);

        return $contact;
    }
}
