<?php

namespace Eilander\Gateway\Eloquent;

use Eilander\Gateway\Contracts\Eloquent as Gateway;
use Eilander\Gateway\Contracts\Presenter;
use Eilander\Gateway\GatewayException;
use Eilander\Repository\Contracts\Eloquent as Repository;
use Eilander\Validator\Contracts\ValidatorInterface as Validator;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentGateway implements Gateway
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var PresenterInterface
     */
    protected $presenter;
    /**
     * @var bool
     */
    protected $skipPresenter = false;
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makePresenter();
        $this->makeRepository();
        $this->makeValidator();
    }

    /**
     * Skip Presenter Wrapper.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipPresenter($status = true)
    {
        $this->skipPresenter = $status;

        return $this;
    }

    /**
     * @param null $presenter
     *
     * @throws GatewayException
     *
     * @return PresenterInterface
     */
    public function makePresenter($presenter = null)
    {
        $presenter = !is_null($presenter) ? $presenter : $this->presenter();
        if (!is_null($presenter)) {
            $this->presenter = is_string($presenter) ? $this->app->make($presenter) : $presenter;
            if (!$this->presenter instanceof Presenter) {
                throw new GatewayException("Class {$presenter} must be an instance of Eilander\\Gateway\\Contracts\\Eloquent");
            }

            return $this->presenter;
        }
    }

    /**
     * @param null $validator
     *
     * @throws GatewayException
     *
     * @return PresenterInterface
     */
    public function makeValidator($validator = null)
    {
        $validator = !is_null($validator) ? $validator : $this->validator();
        if (!is_null($validator)) {
            $this->validator = is_string($validator) ? $this->app->make($validator) : $validator;
            if (!$this->validator instanceof Validator) {
                throw new GatewayException("Class {$validator} must be an instance of Eilander\Validator\Contracts\ValidatorInterface");
            }

            return $this->validator;
        }
    }

    /**
     * @param null $repository
     *
     * @throws GatewayException
     *
     * @return PresenterInterface
     */
    public function makeRepository($repository = null)
    {
        $repository = !is_null($repository) ? $repository : $this->repository();
        if (!is_null($repository)) {
            $this->repository = is_string($repository) ? $this->app->make($repository) : $repository;
            if (!$this->repository instanceof Repository) {
                throw new GatewayException("Class {$repository} must be an instance of Eilander\\Repository\\Contracts\\Repository");
            }

            return $this->repository;
        }
    }

    /**
     * @throws RepositoryException
     */
    public function resetRepository()
    {
        $this->makeRepository();
    }

    /**
     * Wrapper result data.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    protected function parserResult($result)
    {
        if (!$this->skipPresenter && $this->presenter instanceof Presenter) {
            if ($result instanceof Collection || $result instanceof LengthAwarePaginator) {
                $result->each(function ($model) {
                    if ($model instanceof Presentable) {
                        $model->setPresenter($this->presenter);
                    }

                    return $model;
                });
            } elseif ($result instanceof Presentable) {
                $result = $result->setPresenter($this->presenter);
            }

            return $this->presenter->present($result, $this->repository->with);
        }

        return $result;
    }

    public function all()
    {
        // perform any sort of validation first
        return $this->parserResult($this->repository->all());
    }

    public function with($relations)
    {
        // perform any sort of validation first
        $this->repository->with($relations);

        return $this;
    }

    public function create(array $data, $validation = '')
    {
        // some validation
        if ($this->validator instanceof Validator) {
            $rules = [];
            // some validation
            if ($this->validator->fails($data, $validation, $rules)) {
                return $this->validator;
            }
            // perform any sort of validation first
            return $this->parserResult($this->repository->create($data));
        }
    }

    public function update(array $data, $id, $validation = '')
    {
        // some validation
        if ($this->validator instanceof Validator) {
            $rules = [];
            if (method_exists($this->validator, 'update')) {
                $rules = $this->validator->update($id);
            }
            // some validation
            if ($this->validator->fails($data, $validation, $rules)) {
                return $this->validator;
            }
            // perform any sort of validation first
            return $this->parserResult($this->repository->update($data, $id));
        }
    }

    public function show($id)
    {
        // perform any sort of validation first
        return $this->parserResult($this->repository->find($id));
    }

    public function delete($id)
    {
        // perform any sort of validation first
        return $this->repository->delete($id);
    }

    public function paginate($limit = '')
    {
        // perform any sort of validation first
        return $this->parserResult($this->repository->paginate($limit));
    }
}
