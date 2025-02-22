<?php

namespace App\Services;

use App\Abis\AddressBook;
use App\Abis\AutoCompound;
use App\Abis\Claim;
use App\Abis\Downline;
use App\Abis\Factory;
use App\Abis\FurBot;
use App\Abis\FurbPresale;
use App\Abis\LPStaking;
use App\Abis\LPSwap;
use App\Abis\Pair;
use App\Abis\Payment;
use App\Abis\Presale;
use App\Abis\Swap;
use App\Abis\Token;
use App\Abis\Vault;
use App\Abis\Vote;
use App\Abis\LPReserve;
use App\Models\PoolApr;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
        $settings['factory_abi'] = Factory::toString();
        $settings['furbpresale_abi'] = FurbPresale::toString();
        $settings['furbot_abi'] = FurBot::toString();
        $settings['lpstaking_abi'] = LPStaking::toString();
        $settings['lpswap_abi'] = LPSwap::toString();
        $settings['pair_abi'] = Pair::toString();
        $settings['payment_abi'] = Payment::toString();
        $settings['presale_abi'] = Presale::toString();
        $settings['swap_abi'] = Swap::toString();
        $settings['token_abi'] = Token::toString();
        $settings['vault_abi'] = Vault::toString();
        $settings['vote_abi'] = Vote::toString();
        $settings['lpreserve_abi'] = LPReserve::toString();
        $settings['server_time'] = now()->timestamp;
        $settings['referrer'] = session()->get('ref');

        if (!$poolApr = PoolApr::first()) {
            $poolApr = PoolApr::create([
                'alltime' => 100,
                'fourteenday' => 100,
            ]);
        }
        $settings['pool_alltime_apr'] = $poolApr->alltime;
        $settings['pool_fourteenday_apr'] = $poolApr->fourteenday;

        $settings['furio'] = Cache::remember('fur_price', 300, function () use ($settings) {
            try {
                return round(Http::get('https://api.pancakeswap.info/api/v2/tokens/'.$settings['token_address'])->json()['data']['price'], 2);
            } catch (Exception $e) {
                return 0;
            }
        });


        return $settings;
    }
}
