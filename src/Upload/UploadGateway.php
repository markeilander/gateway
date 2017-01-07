<?php

namespace Eilander\Gateway\Upload;

use Eilander\Gateway\Contracts\Presenter;
use Eilander\Gateway\Contracts\Upload as Gateway;
use Eilander\Gateway\GatewayException;
use Eilander\Repository\Contracts\Upload as Repository;
use Eilander\Validator\Contracts\ValidatorInterface as Validator;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UploadGateway implements Gateway
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
        return $result;
    }

    /**
     * Upload file.
     *
     * @param string $file       file to upload
     * @param array  $data
     * @param string $validation reference to validation rules
     *
     * @return mixed
     */
    public function upload($file, array $data = [], $validation = '')
    {
        // some validation
        if ($this->validator instanceof Validator) {
            $rules = [];
            // some validation
            if ($this->validator->fails($data, $validation, $rules)) {
                return $this->validator;
            }
            // perform any sort of validation first
            return $this->parserResult($this->repository->upload($file, $data));
        }
    }
}
