<?php
namespace jobqueue\V1\Rest\Submitter;

class SubmitterResourceFactory
{
    public function __invoke($services)
    {
        $mapper = $services->get('jobqueue\V1\Rest\Submitter\SubmitterMapper');
        return new SubmitterResource($mapper);
    }
}
