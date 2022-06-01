<?php

namespace App\Abis;

use stdClass;

abstract class Abi
{
    /**
     * Invoke.
     *   Returns the abi string for a smart contract.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return json_decode($this->abi ?? '[]');
    }
}
