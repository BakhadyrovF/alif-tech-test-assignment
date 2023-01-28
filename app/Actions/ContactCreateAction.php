<?php

namespace App\Actions;

use App\DTOs\ContactCreateDTO;
use App\Models\Contact;

final class ContactCreateAction
{
    /**
     * @param ContactCreateDTO $dto
     * @return Contact
     */
    public function handle(ContactCreateDTO $dto): Contact
    {
        return Contact::create([
            'name' => $dto->getName()
        ]);
    }
}
