<?php

namespace App\Actions;

use App\DTOs\ContactPhoneCreateDTO;
use App\Enums\ElasticsearchIndex;
use App\Models\ContactPhone;
use App\Services\ElasticsearchService;
use Exception;

final class ContactPhoneCreateAction
{
    /**
     * @param ContactPhoneCreateDTO $dto
     * @param ElasticsearchService $elasticsearchService
     * @return ContactPhone
     * @throws Exception
     */
    public function handle(ContactPhoneCreateDTO $dto, ElasticsearchService $elasticsearchService): ContactPhone
    {
        $contactPhone = ContactPhone::create([
            'phone_number' => $dto->getPhoneNumber(),
            'contact_id' => $dto->getContactId()
        ]);

        $elasticsearchService->updateDocument(ElasticsearchIndex::CONTACT->value, $dto->getContactId(), [
            'script' => [
                'source' => 'ctx._source.phones.add(params.phone_number)',
                'params' => [
                    'phone_number' => $dto->getPhoneNumber()
                ]
            ]
        ]);

        return $contactPhone;
    }
}
