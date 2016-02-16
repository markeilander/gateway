<?php namespace Eilander\Gateway\Elasticsearch;

use Illuminate\Container\Container as Application;
use Eilander\Gateway\GatewayException;
use Eilander\Gateway\BaseGateway;
use Input;

class ElasticsearchGateway extends BaseGateway
{
    /**
     * Get repository by name
     *
     * @param string $name
     * @return repository
     */
    protected function repository($name)
    {
        parent::repository($name);
        return $this->repository;
    }
}