<?php
namespace jobqueue\V1\Rest\Submitter;

use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\Db\TableGateway\TableGateway;

class SubmitterMapper
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
            return new ApiProblem(400, 'Did not find submitter with id ' . $id);
        }
        return $row;
    }

    public function saveSubmitter(SubmitterEntity $submitter)
    {
        $data = [
            'name' => $submitter->name,
            'email' => $submitter->email
        ];

        $id = (int) $submitter->id;

        if ($id == 0) {
            $res = $this->tableGateway->insert($data);
            $submitter->id = $this->tableGateway->lastInsertValue;
            return $submitter;
        } else {
            if ($this->fetchOne($id) instanceof SubmitterEntity) {
                $this->tableGateway->update($data, ['id' => $id]);
                return $submitter;
            } else {
                return new ApiProblem(400, 'Did not find submitter with id ' . $id);
            }
        }
    }

    public function deleteSubmitter($id)
    {
        return $this->tableGateway->delete(['id'=> (int) $id]);
    }
}