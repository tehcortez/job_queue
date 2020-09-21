<?php

use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

require_once __DIR__ . '/vendor/autoload.php';

class RabbitPublisher
{

    private $db;
    private $serviceManager;
    private $rabbitMQConfig;

    public function __construct()
    {
        $configuration = [];
        foreach (Glob::glob('config/{{*}}{{,*.local}}.php', Glob::GLOB_BRACE) as $file) {
            $configuration = ArrayUtils::merge($configuration, include $file);
        }

        // Prepare the service manager
        $smConfig = isset($config['service_manager']) ? $configuration['service_manager'] : [];
        $smConfig = new ServiceManagerConfig($smConfig);

        $this->serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($this->serviceManager);
        $this->serviceManager->setService('ApplicationConfig', $configuration);

        // Load modules
        $this->serviceManager->get('ModuleManager')->loadModules();
        $this->db = $this->serviceManager->get('DBJobQueue');
        $this->rabbitMQConfig = $this->serviceManager->get('Config')['rabbitmq'];
    }

    public function publish()
    {
        $connection = new AMQPStreamConnection(
            $this->rabbitMQConfig["RABBITMQ_HOST"],
            $this->rabbitMQConfig["RABBITMQ_PORT"],
            $this->rabbitMQConfig["RABBITMQ_USERNAME"],
            $this->rabbitMQConfig["RABBITMQ_PASSWORD"]
        );

        $channel = $connection->channel();

        # Create the queue if it does not already exist.
        $channel->queue_declare(
            $queue = $this->rabbitMQConfig["RABBITMQ_QUEUE_NAME"],
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            //    $arguments = array("x-max-priority" => 10),
            //    $arguments = null,
            new AMQPTable(array(
                "x-max-priority" => 10
                )),
            $ticket = null
        );

        $job_id = 0;
        while (true) {
            $command = 'sleep ' . rand(0, 3);
            $priority = rand(0, 9);
            
            $stmt = $this->db->query("INSERT INTO job_list "
                . "(submitter_id, command, priority) VALUES (?, ?, ?);");
            $stmt->execute([
                1,
                $command,
                $priority
            ]);
            
            $job_id = $this->db->getDriver()->getLastGeneratedValue();
            
            $jobArray = array(
                'id' => $job_id,
                'command' => $command,
                'priority' => $priority
            );

            $msg = new AMQPMessage(
                json_encode($jobArray, JSON_UNESCAPED_SLASHES),
                array('delivery_mode' => 2, # make message persistent
                'priority' => $priority) # set message priority
            );

            $channel->basic_publish($msg, '', $this->rabbitMQConfig["RABBITMQ_QUEUE_NAME"]);
            print 'Job created' . PHP_EOL;
            sleep(1);
        }
    }
}

$rabbirPublisher = new RabbitPublisher();
$rabbirPublisher->publish();
