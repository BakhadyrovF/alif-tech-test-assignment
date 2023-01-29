<?php

namespace Database\Seeders;

use App\Enums\ElasticsearchIndex;
use App\Models\Contact;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use App\Services\ElasticsearchService;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        /** Seed contacts with their relations */
        Contact::factory()
            ->has(
                ContactEmail::factory()->count(3),
                'emails'
            )
            ->has(
                ContactPhone::factory()->count(3),
                'phones'
            )
            ->count(100)
            ->create();

        $elasticsearchService = new ElasticsearchService();
        /** If target index does not exist, create it */
        if (!$elasticsearchService->doesIndexExist(ElasticsearchIndex::CONTACT->value)) {
            $elasticsearchService->createIndex(ElasticsearchIndex::CONTACT->value, ElasticsearchIndex::CONTACT->settings());
        }

        /** Index contacts with their relations */
        foreach (Contact::with(['emails', 'phones'])->cursor() as $contact) {
            $elasticsearchService->indexDocument(ElasticsearchIndex::CONTACT->value, $contact->id, [
                'name' => $contact->name,
                'emails' => $contact->emails->pluck('email')->toArray(),
                'phones' => $contact->phones->pluck('phone_number')->toArray()
            ]);
        }
    }
}
