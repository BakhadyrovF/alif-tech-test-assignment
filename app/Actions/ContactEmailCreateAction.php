<?php

namespace App\Actions;

use App\DTOs\ContactEmailCreateDTO;
use App\Enums\ElasticsearchIndex;
use App\Models\ContactEmail;
use App\Services\ElasticsearchService;

final class ContactEmailCreateAction
{
    /**
     * @param ContactEmailCreateDTO $dto
     * @param ElasticsearchService $elasticsearchService
     * @return ContactEmail
     * @throws \Exception
     */
    public function handle(ContactEmailCreateDTO $dto, ElasticsearchService $elasticsearchService): ContactEmail
    {
        $contactEmail = ContactEmail::create([
            'email' => $dto->getEmail(),
            'contact_id' => $dto->getContactId()
        ]);

        $elasticsearchService->updateDocument(ElasticsearchIndex::CONTACT->value, $dto->getContactId(), [
            'script' => [
                'source' => 'ctx._source.emails.add(params.email)',
                'params' => [
                    'email' => $dto->getEmail()
                ]
            ]
        ]);

        return $contactEmail;
    }
}
