<?php
namespace jobqueue\V1\Rpc\Ping;

use Laminas\Mvc\Controller\AbstractActionController;

class PingController extends AbstractActionController
{
    public function pingAction()
    {
        return new \Laminas\ApiTools\ContentNegotiation\ViewModel(
            array(
                'ack' => time(),
            )
        );
    }
}
