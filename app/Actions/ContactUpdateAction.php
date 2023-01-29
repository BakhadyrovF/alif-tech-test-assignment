<?php

namespace App\Actions;

use App\DTOs\ContactUpdateDTO;
use App\Enums\ElasticsearchIndex;
use App\Models\Contact;
use App\Services\ElasticsearchService;
use Exception;

final class ContactUpdateAction
{
    /**
     * @param Contact $contact
     * @param ContactUpdateDTO $dto
     * @param ElasticsearchService $elasticsearchService
     * @return Contact
     * @throws Exception
     */
    public function handle(Contact $contact, ContactUpdateDTO $dto, ElasticsearchService $elasticsearchService): Contact
    {
        $contact->update([
            'name' => $dto->getName()
        ]);

        $elasticsearchService->updateDocument(ElasticsearchIndex::CONTACT->value, $contact->id, [
            'script' => [
                'source' => 'ctx._source.name = params.name',
                'params' => [
                    'name' => $dto->getName()
                ]
            ]
        ]);

        return $contact;
    }
}
