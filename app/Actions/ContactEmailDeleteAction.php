<?php

namespace App\Actions;

use App\Enums\ElasticsearchIndex;
use App\Models\ContactEmail;
use App\Services\ElasticsearchService;
use Exception;

final class ContactEmailDeleteAction
{
    /**
     * @param ContactEmail $contactEmail
     * @param ElasticsearchService $elasticsearchService
     * @return bool
     * @throws Exception
     */
    public function handle(ContactEmail $contactEmail, ElasticsearchService $elasticsearchService): bool
    {
        $elasticsearchService->updateDocument(ElasticsearchIndex::CONTACT->value, $contactEmail->contact_id, [
            'script' => [
                'source' => 'ctx._source.emails.removeIf(e -> e == params.email)',
                'params' => [
                    'email' => $contactEmail->email
                ]
            ]
        ]);

        return $contactEmail->delete();
    }
}
