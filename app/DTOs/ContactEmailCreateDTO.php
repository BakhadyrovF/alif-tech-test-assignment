<?php

namespace App\DTOs;

final class ContactEmailCreateDTO
{
    private int $contactId;

    public function __construct(int $contactId, string $email)
    {
        $this->email = $email;
        $this->contactId = $contactId;
    }
    private string $email;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
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
}
