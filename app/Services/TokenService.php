<?php

namespace App\Services;

use Web3\Contract;

class TokenService
{
    /**
     * Get contract.
     *
     * @return Contract
     */
    protected static function contract(): Contract
    {
        $settings = SettingsService::get();
        return (new Contract($settings['rpc_url'], $settings['token_abi']))->at(AddressBookService::get('token'));
    }

    /**
     * Get total supply.
     *
     * @return string
     */
    public static function totalSupply(): string
    {
        $totalSupply = "0";
        static::contract()->call('totalSupply', function ($err, $result) use (&$totalSupply) {
            if ($err) {
                return;
            }
            $totalSupply = $result[0]->toString();
        });

        return $totalSupply;
    }

    /**
     * Get balance.
     *
     * @return string
     */
    public static function balanceOf(string $address): string
    {
        $balanceOf = "0";
        static::contract()->call('balanceOf', $address, function ($err, $result) use (&$balanceOf) {
            if ($err) {
                return;
            }
            $balanceOf = $result[0]->toString();
        });

        return $balanceOf;
    }
}
