<?php

namespace App\Services;

use App\Abis\AddressBook;
use App\Abis\Claim;
use App\Abis\Downline;
use App\Abis\Payment;
use App\Abis\Swap;
use App\Abis\Token;
use App\Abis\Vault;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Get settings
     *
     * @return array
     */
    public static function get(): array
    {
        $settings = Cache::remember('settings', Carbon::now()->adMinutes(5), function () {
            $settings = [];
            foreach (Setting::orderBy('name')->get() as $setting) {
                $settings[$setting->name] = $setting->value;
            }
            $settings = array_merge($settings, config('settings', []));
            $settings['addressbook_abi'] = AddressBook::toString();
            $settings['claim_abi'] = Claim::toString();
            $settings['downline_abi'] = Downline::toString();
            $settings['payment_abi'] = Payment::toString();
            $settings['swap_abi'] = Swap::toString();
            $settings['token_abi'] = Token::toString();
            $settings['vault_abi'] = Vault::toString();
            return $settings;
        });
        $settings['server_time'] = now()->timestamp;
        $settings['referrer'] = session()->get('ref');

        return $settings;
    }
}
