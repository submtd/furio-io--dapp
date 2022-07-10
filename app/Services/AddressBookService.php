<?php

namespace App\Services;

use Web3\Contract;

class AddressBookService
{
    /**
     * Get address.
     *
     * @param string $contract
     * @return string
     */
    public static function get(string $contract): string
    {
        $settings = SettingsService::get();
        $c = (new Contract($settings['rpc_url'], $settings['addressbook_abi']))->at($settings['addressbook_address']);
        $address = '';
        $c->call('get', $contract, function ($err, $result) use (&$address) {
            if ($err) {
                dd($err);
                return;
            }
            $address = $result[0];
        });

        return $address;
    }
}
