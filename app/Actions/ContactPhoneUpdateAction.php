<?php

namespace App\Actions;

use App\DTOs\ContactPhoneUpdateDTO;
use App\Models\ContactPhone;

final class ContactPhoneUpdateAction
{
    /**
     * @param ContactPhone $contactPhone
     * @param ContactPhoneUpdateDTO $dto
     * @return ContactPhone
     */
    public function handle(ContactPhone $contactPhone, ContactPhoneUpdateDTO $dto): ContactPhone
    {
        $contactPhone->update([
            'phone_number' => $dto->getPhoneNumber()
        ]);

        return $contactPhone;
    }
}
