<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'default_provider' => 'anthropic',

    'providers' => [
        'anthropic' => [
            'handler' => \DV\Assistant\Providers\Anthropic::class,
            'url' => env('ANTHROPIC_URL', 'https://api.anthropic.com/v1/messages'),
            'version' => env('ANTHROPIC_VERSION', '2023-06-01'),
            'api_key' => env('ANTHROPIC_API_KEY'),
        ],
        'tuneai' => [
            'handler' => \DV\Assistant\Providers\TuneAI::class,
            'url' => env('TUNEAI_URL', 'https://proxy.tune.app/chat/completions'),
            'api_key' => env('TUNEAI_API_KEY'),
            'org_id' => env('TUNEAI_ORG_ID'),
        ],
    ],
];
