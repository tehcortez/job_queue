<?php
return [
    'api-tools-content-negotiation' => [
        'selectors' => [],
    ],
    'db' => [
        'adapters' => [
            'dummy' => [],
            'DBJobQueue' => [
                'database' => 'jobqueue',
                'driver' => 'PDO_Mysql',
                'hostname' => '127.0.0.1',
                'username' => 'root',
                'password' => 'myPassword',
            ],
        ],
    ],
    'rabbitmq' => [
        "RABBITMQ_HOST" => "127.0.0.1",
        "RABBITMQ_PORT" => 5672,
        "RABBITMQ_USERNAME" => "root",
        "RABBITMQ_PASSWORD" => "myPassword",
        "RABBITMQ_QUEUE_NAME" => "job_queue"
    ]
];
