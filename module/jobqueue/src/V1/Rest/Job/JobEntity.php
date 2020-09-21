<?php
namespace jobqueue\V1\Rest\Job;

class JobEntity
{
    public $id;
    public $submitterId;
    public $processorId;
    public $command;
    public $priority;
    
    public function getArrayCopy()
    {
        return array(
            'id' => $this->id,
            'submitter_id' => $this->submitterId,
            'processor_id' => $this->processorId,
            'command' => $this->command,
            'priority' => $this->priority
        );
    }

    public function exchangeArray(array $array)
    {
        $this->id = $array['id'];
        $this->submitterId = $array['submitter_id'];
        $this->processorId = $array['processor_id'];
        $this->command = $array['command'];
        $this->priority = $array['priority'];
    }
}
