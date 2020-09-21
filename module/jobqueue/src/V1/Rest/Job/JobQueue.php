<?php
namespace jobqueue\V1\Rest\Job;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Laminas\Db\TableGateway\TableGateway;

class JobQueue
{

    protected $rabbitMQConfig;
    protected $connection;
    protected $channel;

    public function __construct($rabbitMQConfig, TableGateway $tableGateway)
    {
        $this->rabbitMQConfig = $rabbitMQConfig;
        
        $this->connection = $this->getConnection($rabbitMQConfig);

        $this->channel = $this->connection->channel();

        # Create the queue if it does not already exist.
        $this->channel->queue_declare(
            $queue = $this->rabbitMQConfig["RABBITMQ_QUEUE_NAME"],
            $passive = false,
            $durable = true,
            $exclusive = false,
            $auto_delete = false,
            $nowait = false,
            new AMQPTable(array(
                "x-max-priority" => 10
                )),
            $ticket = null
        );
    }

    public function publishMessage($id, $command, $priority)
    {
//        echo 'so far s';die;
        $job_id = $id;
//        $command = 'sleep ' . rand(0, 3);
//        $priority = rand(0, 9);

//        $stmt = $this->db->query("INSERT INTO job_list "
//            . "(submitter_id, command, priority) VALUES (?, ?, ?);");
//        $stmt->execute([
//            1,
//            $command,
//            $priority
//        ]);

//        $job_id = $this->db->getDriver()->getLastGeneratedValue();

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

        $this->channel->basic_publish($msg, '', $this->rabbitMQConfig["RABBITMQ_QUEUE_NAME"]);
        return true;
//        print 'Job created' . PHP_EOL;
//        sleep(1);
    }

    public function getConnection($rabbitMQConfig)
    {
        $connection = new AMQPStreamConnection(
            $rabbitMQConfig["RABBITMQ_HOST"],
            $rabbitMQConfig["RABBITMQ_PORT"],
            $rabbitMQConfig["RABBITMQ_USERNAME"],
            $rabbitMQConfig["RABBITMQ_PASSWORD"]
        );
        return $connection;
    }
}
