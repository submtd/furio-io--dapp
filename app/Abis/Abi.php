<?php

namespace App\Abis;

abstract class Abi
{
    /**
     * Invoke.
     *   Returns the abi string for a smart contract.
     *
     * @return string
     */
    public function __invoke(): string
    {
        return $this->abi ?? '[]';
    }

    /**
     * To array
     *
     * @return array
     */
    public static function toArray(): array
    {
        return json_decode((new static)());
    }

    /**
     * To string
     *
     * @return string
     */
    public static function toString(): string
    {
        return (new static)();
    }
}
