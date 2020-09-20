<?php
return [
    'jobqueue\\V1\\Rpc\\Ping\\Controller' => [
        'description' => 'Ping the API',
        'GET' => [
            'description' => 'Ping the API for acknowledgement',
            'response' => '{
   "ack": "Acknowledge the request with a timestamp"
}',
        ],
    ],
    'jobqueue\\V1\\Rest\\Submitter\\Controller' => [
        'description' => 'Service responsible for submitter administration',
        'collection' => [
            'description' => 'Collection of submitter data',
            'GET' => [
                'description' => 'Gets list of submitters',
                'response' => '{
   "_links": {
       "self": {
           "href": "/submitter"
       },
       "first": {
           "href": "/submitter?page={page}"
       },
       "prev": {
           "href": "/submitter?page={page}"
       },
       "next": {
           "href": "/submitter?page={page}"
       },
       "last": {
           "href": "/submitter?page={page}"
       }
   }
   "_embedded": {
       "submitter": [
           {
               "_links": {
                   "self": {
                       "href": "/submitter[/:submitter_id]"
                   }
               }
              "name": "Name of submitter",
              "email": "E-mail of submitter"
           }
       ]
   }
}',
            ],
            'POST' => [
                'description' => 'Include new submitter',
                'request' => '{
   "name": "Name of submitter",
   "email": "E-mail of submitter"
}',
                'response' => '{
   "_links": {
       "self": {
           "href": "/submitter[/:submitter_id]"
       }
   }
   "name": "Name of submitter",
   "email": "E-mail of submitter"
}',
            ],
        ],
        'entity' => [
            'description' => 'App submitter',
            'GET' => [
                'description' => 'Gets specific submitter from app',
                'response' => '{
   "_links": {
       "self": {
           "href": "/submitter[/:submitter_id]"
       }
   }
   "name": "Name of submitter",
   "email": "E-mail of submitter"
}',
            ],
            'DELETE' => [
                'description' => 'Remove submitter from app',
                'request' => '{
   "name": "Name of submitter",
   "email": "E-mail of submitter"
}',
                'response' => '{
   "_links": {
       "self": {
           "href": "/submitter[/:submitter_id]"
       }
   }
   "name": "Name of submitter",
   "email": "E-mail of submitter"
}',
            ],
        ],
    ],
];
