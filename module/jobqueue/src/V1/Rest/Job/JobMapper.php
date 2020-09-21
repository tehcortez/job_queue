<?php
namespace jobqueue\V1\Rest\Job;

use Laminas\Db\TableGateway\TableGateway;

class JobMapper
{

    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
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
            throw new Exception('Did not find job with id ' . $id);
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
            return $job;
        } else {
            if ($this->fetchOne($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
                return $job;
            }
        }
    }

    public function deleteJob($id)
    {
        return $this->tableGateway->delete(['id'=> (int) $id]);
    }
}