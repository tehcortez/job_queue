<?php
namespace jobqueue\V1\Rest\Job;

class JobQueueFactory
{

    public function __invoke($sm)
    {
        $tableGateway = $sm->get('JobsTableGateway');
        $rabbitMQConfig = $sm->get('Config')['rabbitmq'];
        return new JobQueue($rabbitMQConfig, $tableGateway);
    }
}
