<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'info' => [
                'title' => 'News Aggregator API Documentation',
                'description' => 'Documentation for our news aggregator api.',
                'version' => '1.0.0',
            ],
            'routes' => [
                'api' => 'api/documentation',
            ],
            'paths' => [
                'base' => env('L5_SWAGGER_BASE_PATH', '/api'),
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',
                'docs' => storage_path('api-docs'),
                'format_to_use_for_docs' => 'json',
                'annotations' => [
                    base_path('app'),
                ]
            ],
        ],
    ],
];
