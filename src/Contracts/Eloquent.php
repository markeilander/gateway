<?php
namespace Eilander\Gateway\Contracts;

/**
 * Interface Gateway
 * @package Eilander\Gateway\Contracts
 */
interface Eloquent extends Gateway
{
    /**
     * Set Presenter
     *
     * @param $presenter
     * @return mixed
     */
    public function makePresenter($presenter);
    /**
     * Set Validator
     *
     * @param $validator
     * @return mixed
     */
    public function makeValidator($validator);
    /**
     * Skip Presenter Wrapper
     *
     * @param bool $status
     * @return $this
     */
    public function skipPresenter($status = true);
}