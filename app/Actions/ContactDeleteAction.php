<?php

namespace App\Actions;

use App\Enums\ElasticsearchIndex;
use App\Models\Contact;
use App\Services\ElasticsearchService;
use Exception;

final class ContactDeleteAction
{
    /**
     * @param Contact $contact
     * @param ElasticsearchService $elasticsearchService
     * @return bool
     * @throws Exception
     */
    public function handle(Contact $contact, ElasticsearchService $elasticsearchService): bool
    {
        $elasticsearchService->deleteDocument(ElasticsearchIndex::CONTACT->value, $contact->id);

        return $contact->delete();
    }
}
