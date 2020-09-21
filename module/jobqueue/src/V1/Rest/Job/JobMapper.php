<?php
namespace jobqueue\V1\Rest\Job;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\Db\TableGateway\TableGateway;

class JobMapper
{

    protected $tableGateway;
    protected $jobQueue;

    public function __construct(TableGateway $tableGateway, JobQueue $jobQueue)
    {
        $this->tableGateway = $tableGateway;
        $this->jobQueue = $jobQueue;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function fetchOne($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            return new ApiProblem(400, 'Did not find job with id ' . $id);
        }
        return $row;
    }

    public function saveJob(JobEntity $job)
    {
        $data = [
            'submitter_id' => $job->submitterId,
//            'processor_id' => $job->processorId,
            'command' => $job->command,
            'priority' => $job->priority
        ];

        $id = (int) $job->id;

        if ($id == 0) {
            $res = $this->tableGateway->insert($data);
            $job->id = $this->tableGateway->lastInsertValue;
            $this->jobQueue->publishMessage($job->id, $job->command, $job->priority);
            return $job;
        } else {
            if ($this->fetchOne($id) instanceof JobEntity) {
                $this->tableGateway->update($data, ['id' => $id]);
                return $job;
            } else {
                return new ApiProblem(400, 'Did not find job with id ' . $id);
            }
        }
    }

    public function deleteJob($id)
    {
        return $this->tableGateway->delete(['id' => (int) $id]);
    }
}
