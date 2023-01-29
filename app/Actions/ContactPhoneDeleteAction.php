<?php

namespace App\Actions;

use App\Enums\ElasticsearchIndex;
use App\Models\ContactPhone;
use App\Services\ElasticsearchService;
use Exception;

final class ContactPhoneDeleteAction
{
    /**
     * @param ContactPhone $contactPhone
     * @param ElasticsearchService $elasticsearchService
     * @return bool
     * @throws Exception
     */
    public function handle(ContactPhone $contactPhone, ElasticsearchService $elasticsearchService): bool
    {
        $elasticsearchService->updateDocument(ElasticsearchIndex::CONTACT->value, $contactPhone->contact_id, [
            'script' => [
                'source' => 'ctx._source.phones.removeIf(p -> p == params.phone_number)',
                'params' => [
                    'phone_number' => $contactPhone->phone_number
                ]
            ]
        ]);

        return $contactPhone->delete();
    }
}
