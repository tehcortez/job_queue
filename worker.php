<?php

use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\Glob;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

require_once __DIR__ . '/vendor/autoload.php';

class RabbitWorker
{

    private $db;
    private $serviceManager;
    private $rabbitMQConfig;
    private $workerId;

    public function __construct($workerName)
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

        $this->workerId = null;
        $stmt = $this->db->query("SELECT id FROM processors WHERE name = ?");
        $processorResults = $stmt->execute([$workerName]);
        foreach ($processorResults as $processorResult) {
            $this->workerId = $processorResult['id'];
        }
        if (!isset($this->workerId)) {
            $stmt = $this->db->query("INSERT INTO processors "
                . "(name) VALUES (?)");
            $stmt->execute([$workerName]);

            $this->workerId = $this->db->getDriver()->getLastGeneratedValue();
        }
    }

    public function work()
    {
        $connection = new AMQPStreamConnection(
            $this->rabbitMQConfig["RABBITMQ_HOST"],
            $this->rabbitMQConfig["RABBITMQ_PORT"],
            $this->rabbitMQConfig["RABBITMQ_USERNAME"],
            $this->rabbitMQConfig["RABBITMQ_PASSWORD"]
        );

        $channel = $connection->channel();

        # Create the queue if it doesnt already exist.
        $channel->queue_declare(
            $queue = $this->rabbitMQConfig["RABBITMQ_QUEUE_NAME"],
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            //    $arguments = null,
            new AMQPTable(array(
                "x-max-priority" => 10
                )),
            $ticket = null
        );


        echo ' [*] Waiting for messages. To exit press CTRL+C', "\n";

        $callback = function($msg) {
            echo " [x] Received ", $msg->body, "\n";
            $job = json_decode($msg->body, $assocForm = true);
            $stmt = $this->db->query("SELECT command FROM job_list "
                . "WHERE id = ?");
            $results = $stmt->execute([$job['id']]);
            foreach ($results as $result) {
                $command = $result['command'];
            }

            $output = shell_exec($command);
            echo " Output: $output \n";

            $stmt = $this->db->query("UPDATE job_list "
                . "SET processor_id=?, executed_at=CURRENT_TIMESTAMP WHERE id = ?");
            $results = $stmt->execute([
                $this->workerId,
                $job['id']
            ]);

            echo " [x] Done", "\n";
            $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        };

        $channel->basic_qos(null, 1, null);

        $channel->basic_consume(
            $queue = $this->rabbitMQConfig["RABBITMQ_QUEUE_NAME"],
            $consumer_tag = '',
            $no_local = false,
            $no_ack = false,
            $exclusive = false,
            $nowait = false,
            $callback
        );

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}

if (isset($argv[1])) {
    echo ' Worker name is: '.$argv[1], "\n";
	$rabbitWorker = new RabbitWorker($argv[1]);
    $rabbitWorker->work();
}
else {
	echo "Please provide a processor name \n";
}
die;


