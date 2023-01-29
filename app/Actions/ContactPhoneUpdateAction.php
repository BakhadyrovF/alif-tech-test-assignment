<?php

namespace App\Actions;

use App\DTOs\ContactPhoneUpdateDTO;
use App\Enums\ElasticsearchIndex;
use App\Models\ContactPhone;
use App\Services\ElasticsearchService;
use Exception;

final class ContactPhoneUpdateAction
{
    /**
     * @param ContactPhone $contactPhone
     * @param ContactPhoneUpdateDTO $dto
     * @param ElasticsearchService $elasticsearchService
     * @return ContactPhone
     * @throws Exception
     */
    public function handle(ContactPhone $contactPhone, ContactPhoneUpdateDTO $dto, ElasticsearchService $elasticsearchService): ContactPhone
    {
        if ($contactPhone->phone_number !== $dto->getPhoneNumber()) {
            $elasticsearchService->updateDocument(ElasticsearchIndex::CONTACT->value, $contactPhone->contact_id, [
                'script' => [
                    'source' => 'ctx._source.phones.removeIf(p -> p == params.old_phone_number); ctx._source.phones.add(params.new_phone_number)',
                    'params' => [
                        'old_phone_number' => $contactPhone->phone_number,
                        'new_phone_number' => $dto->getPhoneNumber()
                    ]
                ]
            ]);
        }

        $contactPhone->update([
            'phone_number' => $dto->getPhoneNumber()
        ]);

        return $contactPhone;
    }
}
