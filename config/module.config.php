<?php
return [
    'controllers' => [
        'invokables' => [
            'IIIFHosting\Controller\Ingest' => 'IIIFHosting\Controller\IngestController',
        ],
    ],
    'router' => [
        'routes' => [
            'IIIFHosting' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/iiif_ingest',
                    'defaults' => [
                        '__NAMESPACE__' => 'IIIFHosting\Controller',
                        'controller' => 'Ingest',
                        'action' => 'ingest',
                    ],
                ],
            ],
        ],
    ],
];
