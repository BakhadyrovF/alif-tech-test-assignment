<?php

namespace App\Actions;

use App\DTOs\ContactPhoneCreateDTO;
use App\Models\ContactPhone;

final class ContactPhoneCreateAction
{
    public function handle(ContactPhoneCreateDTO $dto): ContactPhone
    {
        return ContactPhone::create([
            'phone_number' => $dto->getPhoneNumber(),
            'contact_id' => $dto->getContactId()
        ]);
    }
}
