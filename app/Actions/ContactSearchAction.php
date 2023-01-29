<?php

namespace App\Actions;

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
    public function handle(string $searchable, ElasticsearchService $elasticsearchService): Collection
    {
        return collect($elasticsearchService->search(ElasticsearchIndex::CONTACT->value, [
            'size' => 100,
            'query' => [
                'multi_match' => [
                    'query' => $searchable,
                    'type' => 'bool_prefix',
                    'fields' => ['name', 'emails', 'phones']
                ]
            ],
            '_source' => false
        ]))->pluck('_id');
    }
}
