<?php

namespace App\Services;

use App\Abis\AddressBook;
use App\Abis\AutoCompound;
use App\Abis\Claim;
use App\Abis\Downline;
use App\Abis\FurbPresale;
use App\Abis\Payment;
use App\Abis\Presale;
use App\Abis\Swap;
use App\Abis\Token;
use App\Abis\Vault;
use App\Abis\Vote;

class SettingsService
{
    /**
     * Get settings
     *
     * @return array
     */
    public static function get(): array
    {
        $settings = config('settings', []);
        $settings['addressbook_abi'] = AddressBook::toString();
        $settings['autocompound_abi'] = AutoCompound::toString();
        $settings['claim_abi'] = Claim::toString();
        $settings['downline_abi'] = Downline::toString();
        $settings['furbpresale_abi'] = FurbPresale::toString();
        $settings['payment_abi'] = Payment::toString();
        $settings['presale_abi'] = Presale::toString();
        $settings['swap_abi'] = Swap::toString();
        $settings['token_abi'] = Token::toString();
        $settings['vault_abi'] = Vault::toString();
        $settings['vote_abi'] = Vote::toString();
        $settings['server_time'] = now()->timestamp;
        $settings['referrer'] = session()->get('ref');

        return $settings;
    }
}
