<?php

namespace App\Actions;

use App\DTOs\ContactSearchDTO;
use App\Enums\ElasticsearchIndex;
use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Support\Collection;

final class ContactSearchAction
{
    /**
     * @param string $searchable
     * @param ElasticsearchService $elasticsearchService
     * @return Collection
     * @throws Exception
     */
    public function handle(ContactSearchDTO $dto, ElasticsearchService $elasticsearchService): Collection
    {
        return collect($elasticsearchService->search(ElasticsearchIndex::CONTACT->value, [
            'size' => 100,
            'query' => [
                'multi_match' => [
                    'query' => $dto->getQuery(),
                    'type' => 'bool_prefix',
                    'fields' => ['name', 'emails', 'phones']
                ]
            ],
            '_source' => false
        ]))->pluck('_id');
    }
}
