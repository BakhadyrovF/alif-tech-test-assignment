<?php

namespace Database\Seeders;


use App\Models\Contact;
use App\Models\ContactEmail;
use App\Models\ContactPhone;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
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
    }
}
