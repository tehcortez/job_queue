<?php
namespace jobqueue\V1\Rest\Job;

class JobResourceFactory
{

    public function __invoke($services)
    {
        $mapper = $services->get('jobqueue\V1\Rest\Job\JobMapper');
        return new JobResource($mapper);
    }
}
