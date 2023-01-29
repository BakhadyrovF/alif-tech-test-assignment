<?php

namespace App\DTOs;

final class ContactPhoneCreateDTO
{
    private int $contactId;
    private string $phoneNumber;

    public function __construct(int $contactId, string $phoneNumber)
    {
        $this->contactId = $contactId;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return int
     */
    public function getContactId(): int
    {
        return $this->contactId;
    }

    /**
     * @param int $contactId
     */
    public function setContactId(int $contactId): void
    {
        $this->contactId = $contactId;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }
}
