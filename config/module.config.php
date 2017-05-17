<?php
return [
    'controllers' => [
        'invokables' => [
            'IIIFHosting\Controller\Ingest' => 'IIIFHosting\Controller\IngestController',
            'IIIFHosting\Controller\Manifest' => 'IIIFHosting\Controller\ManifestController',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'Manifest' => 'IIIFHosting\Service\ViewHelper\ManifestFactory',
        ],
    ],
    'router' => [
        'routes' => [
            'iiif_ingest' => [
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
            'iiif_manifest' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/:id/manifest.json',
                    'constraints' => [
                        'id' => '\d+',
                    ],
                    'defaults' => [
                        '__NAMESPACE__' => 'IIIFHosting\Controller',
                        'controller' => 'Manifest',
                        'action' => 'manifest',
                    ],
                ],
            ],
        ],
    ],
];
