<?php

namespace Eilander\Gateway\Contracts;

/**
 * Interface PresenterInterface.
 */
interface Presenter
{
    /**
     * Prepare data to present.
     *
     * @param $data
     *
     * @return mixed
     */
    public function present($data, $includes);
}
