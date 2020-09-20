<?php

namespace jobqueue\V1\Rest\Submitter;

use Laminas\Db\TableGateway\TableGateway;

class SubmitterMapperFactory
{
    public function __invoke($services)
    {
        $dbAdapter = $services->get('DBJobQueue');
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype($services->get(SubmitterEntity::class));
        return new SubmitterMapper(new TableGateway('submitter', $dbAdapter, null, $resultSetPrototype));
    }
}
