<?php

namespace App\Actions;

use App\DTOs\ContactEmailUpdateDTO;
use App\Enums\ElasticsearchIndex;
use App\Models\ContactEmail;
use App\Services\ElasticsearchService;
use Exception;

final class ContactEmailUpdateAction
{
    /**
     * @param ContactEmail $contactEmail
     * @param ContactEmailUpdateDTO $dto
     * @param ElasticsearchService $elasticsearchService
     * @return ContactEmail
     * @throws Exception
     */
    public function handle(ContactEmail $contactEmail, ContactEmailUpdateDTO $dto, ElasticsearchService $elasticsearchService): ContactEmail
    {
        if ($contactEmail->email !== $dto->getEmail()) {
            $elasticsearchService->updateDocument(ElasticsearchIndex::CONTACT->value, $contactEmail->contact_id, [
                'script' => [
                    'source' => 'ctx._source.emails.removeIf(e -> e == params.old_email); ctx._source.emails.add(params.new_email)',
                    'params' => [
                        'old_email' => $contactEmail->email,
                        'new_email' => $dto->getEmail()
                    ]
                ]
            ]);
        }

        $contactEmail->update([
            'email' => $dto->getEmail()
        ]);

        return $contactEmail;
    }
}
