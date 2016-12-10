<?php

namespace Eilander\Gateway\Presenter;

use Eilander\Gateway\Contracts\Presenter;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\SerializerAbstract;

/**
 * Class FractalPresenter.
 */
abstract class FractalPresenter implements Presenter
{
    /**
     * @var string
     */
    protected $resourceKeyItem = null;
    /**
     * @var string
     */
    protected $resourceKeyCollection = null;
    /**
     * @var \League\Fractal\Manager
     */
    protected $fractal = null;
    /**
     * @var \League\Fractal\Resource\Collection
     */
    protected $resource = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new Exception(trans('gateway::packages.league_fractal_required'));
        }
        $this->fractal = new Manager();
        $this->setupSerializer();
    }

    /**
     * @return $this
     */
    protected function setupSerializer()
    {
        $serializer = $this->serializer();
        if ($serializer instanceof SerializerAbstract) {
            $this->fractal->setSerializer(new $serializer());
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function parseIncludes($includes = '')
    {
        if (trim($includes) != '') {
            $this->fractal->parseIncludes($includes);
        } else {
            $request = app('Illuminate\Http\Request');
            $paramIncludes = config('gateway.fractal.params.include', 'include');
            if ($request->has($paramIncludes)) {
                $params = str_replace(';', ',', $request->get($paramIncludes));
                $this->fractal->parseIncludes($params);
            }
        }

        return $this;
    }

    /**
     * Get Serializer.
     *
     * @return SerializerAbstract
     */
    public function serializer()
    {
        $serializer = config('gateway.fractal.serializer', 'League\\Fractal\\Serializer\\DataArraySerializer');

        return new $serializer();
    }

    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    abstract public function getTransformer();

    /**
     * Prepare data to present.
     *
     * @param $data
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function present($data, $includes = '')
    {
        if (!class_exists('League\Fractal\Manager')) {
            throw new Exception(trans('gateway::packages.league_fractal_required'));
        }
        $this->parseIncludes($includes);
        if ($data instanceof EloquentCollection) {
            $this->resource = $this->transformCollection($data);
        } elseif ($data instanceof AbstractPaginator) {
            $this->resource = $this->transformPaginator($data);
        } else {
            $this->resource = $this->transformItem($data);
        }

        return $this->fractal->createData($this->resource)->toArray();
    }

    /**
     * @param $data
     *
     * @return Item
     */
    protected function transformItem($data)
    {
        return new Item($data, $this->getTransformer(), $this->resourceKeyItem);
    }

    /**
     * @param $data
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function transformCollection($data)
    {
        return new Collection($data, $this->getTransformer(), $this->resourceKeyCollection);
    }

    /**
     * @param AbstractPaginator|LengthAwarePaginator|Paginator $paginator
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function transformPaginator($paginator)
    {
        $collection = $paginator->getCollection();
        $resource = new Collection($collection, $this->getTransformer(), $this->resourceKeyCollection);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return $resource;
    }
}
