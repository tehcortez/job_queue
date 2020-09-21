<?php
namespace jobqueue;

use jobqueue\V1\Rest\Job\JobEntity;
use jobqueue\V1\Rest\Job\JobMapper;
use jobqueue\V1\Rest\Job\JobQueue;
use jobqueue\V1\Rest\Submitter\SubmitterEntity;
use jobqueue\V1\Rest\Submitter\SubmitterMapper;
use Laminas\ApiTools\Provider\ApiToolsProviderInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;

class Module implements ApiToolsProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'JobSubmitterTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('DBJobQueue');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SubmitterEntity());
                    return new TableGateway('submitters', $dbAdapter, null, $resultSetPrototype);
                },
                'jobqueue\V1\Rest\Submitter\SubmitterMapper' => function($sm) {
                    $tableGateway = $sm->get('JobSubmitterTableGateway');
                    return new SubmitterMapper($tableGateway);
                },
                'JobsTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('DBJobQueue');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new JobEntity());
                    return new TableGateway('job_list', $dbAdapter, null, $resultSetPrototype);
                },
                'jobqueue\V1\Rest\Job\JobMapper' => function($sm) {
                    $tableGateway = $sm->get('JobsTableGateway');
                    $jobQueue = $sm->get(JobQueue::class);
                    return new JobMapper($tableGateway, $jobQueue);
                }
//                'jobqueue\V1\Rest\Job\JobQueue' => function($sm) {
//                    $rabbitMQConfig = $sm->get('Config')['rabbitmq'];
//                    return new JobQueue($rabbitMQConfig);
//                },
//                JobQueue::class => function($sm) {
//                    $rabbitMQConfig = $sm->get('Config')['rabbitmq'];
//                    return new JobQueue($rabbitMQConfig);
//                }
            )
        );
    }

    public function getAutoloaderConfig()
    {
        return [
            'Laminas\ApiTools\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }
}
