<?php

namespace Eilander\Gateway;

class GatewayException extends \Exception
{
    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'Gateway error. Sorry!';
}