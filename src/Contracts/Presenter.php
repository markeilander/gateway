<?php
namespace Eilander\Gateway\Contracts;
/**
 * Interface PresenterInterface
 * @package Eilander\Gateway\Contracts
 */
interface Presenter
{
    /**
     * Prepare data to present
     *
     * @param $data
     * @return mixed
     */
    public function present($data, $includes);
}