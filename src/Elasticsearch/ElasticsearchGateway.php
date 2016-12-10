<?php

namespace Eilander\Gateway\Elasticsearch;

use Eilander\Gateway\BaseGateway;

class ElasticsearchGateway extends BaseGateway
{
    /**
     * Get repository by name.
     *
     * @param string $name
     *
     * @return repository
     */
    protected function repository($name)
    {
        parent::repository($name);

        return $this->repository;
    }
}
