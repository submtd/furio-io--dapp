<?php

namespace App\Abis;

use stdClass;

abstract class Abi
{
    /**
     * Invoke.
     *   Returns the abi string for a smart contract.
     *
     * @return string
     */
    public function __invoke(): stdClass
    {
        return json_decode($this->abi ?? '');
    }
}
