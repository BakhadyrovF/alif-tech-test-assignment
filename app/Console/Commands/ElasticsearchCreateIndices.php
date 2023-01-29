<?php

namespace App\Console\Commands;

use App\Enums\ElasticsearchIndex;
use App\Services\ElasticsearchService;
use Exception;
use Illuminate\Console\Command;

class ElasticsearchCreateIndices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch-indices:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Elasticsearch Indices';

    /**
     * Execute the console command.
     *
     * @param ElasticsearchService $elasticsearchService
     * @return void
     * @throws Exception
     */
    public function handle(ElasticsearchService $elasticsearchService): void
    {
        /** Contacts index */
        $elasticsearchService->deleteIndex(ElasticsearchIndex::CONTACT->value);
        $elasticsearchService->createIndex(ElasticsearchIndex::CONTACT->value, ElasticsearchIndex::CONTACT->settings());
    }
}
