<?php

namespace App\Enums;

enum ElasticsearchIndex: string
{
    case CONTACT = 'contacts';

    public function settings(): array
    {
        return match ($this) {
            self::CONTACT => [
                'mappings' => [
                    'properties' => [
                        'name' => [
                            'type' => 'text'
                        ],
                        'emails' => [
                            'type' => 'text'
                        ],
                        'phones' => [
                            'type' => 'text'
                        ]
                    ]
                ]
            ]
        };
    }
}
