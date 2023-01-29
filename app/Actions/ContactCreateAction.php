<?php

namespace App\Actions;

use App\DTOs\ContactCreateDTO;
use App\Enums\ElasticsearchIndex;
use App\Models\Contact;
use App\Services\ElasticsearchService;
use Exception;

final class ContactCreateAction
{
    /**
     * @param ContactCreateDTO $dto
     * @param ElasticsearchService $elasticsearchService
     * @return Contact
     * @throws Exception
     */
    public function handle(ContactCreateDTO $dto, ElasticsearchService $elasticsearchService): Contact
    {
        $contact = Contact::create([
            'name' => $dto->getName()
        ]);

        $elasticsearchService->indexDocument(ElasticsearchIndex::CONTACT->value, $contact->id, [
            'name' => $dto->getName(),
            'emails' => [],
            'phones' => []
        ]);

        return $contact;
    }
}
