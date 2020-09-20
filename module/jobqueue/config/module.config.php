<?php
return [
    'controllers' => [
        'factories' => [
            'jobqueue\\V1\\Rpc\\Ping\\Controller' => \jobqueue\V1\Rpc\Ping\PingControllerFactory::class,
        ],
    ],
    'router' => [
        'routes' => [
            'jobqueue.rpc.ping' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/ping',
                    'defaults' => [
                        'controller' => 'jobqueue\\V1\\Rpc\\Ping\\Controller',
                        'action' => 'ping',
                    ],
                ],
            ],
            'jobqueue.rest.submitter' => [
                'type' => 'Segment',
                'options' => [
                    'route' => '/submitter[/:submitter_id]',
                    'defaults' => [
                        'controller' => 'jobqueue\\V1\\Rest\\Submitter\\Controller',
                    ],
                ],
            ],
        ],
    ],
    'api-tools-versioning' => [
        'uri' => [
            0 => 'jobqueue.rpc.ping',
            1 => 'jobqueue.rest.submitter',
        ],
    ],
    'api-tools-rpc' => [
        'jobqueue\\V1\\Rpc\\Ping\\Controller' => [
            'service_name' => 'ping',
            'http_methods' => [
                0 => 'GET',
            ],
            'route_name' => 'jobqueue.rpc.ping',
        ],
    ],
    'api-tools-content-negotiation' => [
        'controllers' => [
            'jobqueue\\V1\\Rpc\\Ping\\Controller' => 'Json',
            'jobqueue\\V1\\Rest\\Submitter\\Controller' => 'HalJson',
        ],
        'accept_whitelist' => [
            'jobqueue\\V1\\Rpc\\Ping\\Controller' => [
                0 => 'application/vnd.jobqueue.v1+json',
                1 => 'application/json',
                2 => 'application/*+json',
            ],
            'jobqueue\\V1\\Rest\\Submitter\\Controller' => [
                0 => 'application/vnd.jobqueue.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ],
        ],
        'content_type_whitelist' => [
            'jobqueue\\V1\\Rpc\\Ping\\Controller' => [
                0 => 'application/vnd.jobqueue.v1+json',
                1 => 'application/json',
            ],
            'jobqueue\\V1\\Rest\\Submitter\\Controller' => [
                0 => 'application/vnd.jobqueue.v1+json',
                1 => 'application/json',
            ],
        ],
    ],
    'api-tools-content-validation' => [
        'jobqueue\\V1\\Rpc\\Ping\\Controller' => [
            'input_filter' => 'jobqueue\\V1\\Rpc\\Ping\\Validator',
        ],
        'jobqueue\\V1\\Rest\\Submitter\\Controller' => [
            'input_filter' => 'jobqueue\\V1\\Rest\\Submitter\\Validator',
        ],
    ],
    'input_filter_specs' => [
        'jobqueue\\V1\\Rpc\\Ping\\Validator' => [
            0 => [
                'required' => true,
                'validators' => [],
                'filters' => [],
                'name' => 'ack',
                'description' => 'Acknowledge the request with a timestamp',
            ],
        ],
        'jobqueue\\V1\\Rest\\Submitter\\Validator' => [
            0 => [
                'required' => true,
                'validators' => [
                    0 => [
                        'name' => \Laminas\Validator\NotEmpty::class,
                        'options' => [],
                    ],
                ],
                'filters' => [
                    0 => [
                        'name' => \Laminas\Filter\StringTrim::class,
                        'options' => [],
                    ],
                    1 => [
                        'name' => \Laminas\Filter\StringToLower::class,
                        'options' => [],
                    ],
                ],
                'name' => 'name',
                'description' => 'Name of submitter',
                'field_type' => '',
            ],
            1 => [
                'required' => true,
                'validators' => [
                    0 => [
                        'name' => \Laminas\Validator\EmailAddress::class,
                        'options' => [],
                    ],
                ],
                'filters' => [],
                'name' => 'email',
                'description' => 'E-mail of submitter',
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            \jobqueue\V1\Rest\Submitter\SubmitterResource::class => \jobqueue\V1\Rest\Submitter\SubmitterResourceFactory::class,
        ],
    ],
    'api-tools-rest' => [
        'jobqueue\\V1\\Rest\\Submitter\\Controller' => [
            'listener' => \jobqueue\V1\Rest\Submitter\SubmitterResource::class,
            'route_name' => 'jobqueue.rest.submitter',
            'route_identifier_name' => 'submitter_id',
            'collection_name' => 'submitter',
            'entity_http_methods' => [
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ],
            'collection_http_methods' => [
                0 => 'GET',
                1 => 'POST',
            ],
            'collection_query_whitelist' => [],
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => \jobqueue\V1\Rest\Submitter\SubmitterEntity::class,
            'collection_class' => \jobqueue\V1\Rest\Submitter\SubmitterCollection::class,
            'service_name' => 'Submitter',
        ],
    ],
    'api-tools-hal' => [
        'metadata_map' => [
            \jobqueue\V1\Rest\Submitter\SubmitterEntity::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'jobqueue.rest.submitter',
                'route_identifier_name' => 'submitter_id',
                'hydrator' => \Laminas\Hydrator\ArraySerializable::class,
            ],
            \jobqueue\V1\Rest\Submitter\SubmitterCollection::class => [
                'entity_identifier_name' => 'id',
                'route_name' => 'jobqueue.rest.submitter',
                'route_identifier_name' => 'submitter_id',
                'is_collection' => true,
            ],
        ],
    ],
];
