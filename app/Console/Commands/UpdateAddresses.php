<?php

namespace App\Console\Commands;

use App\Models\Price;
use App\Models\Setting;
use App\Services\AddressBookService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the addresses in the Furio ecosystem.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateSetting('claim_address', AddressBookService::get('claim'));
        $this->updateSetting('downline_address', AddressBookService::get('downline'));
        $this->updateSetting('presale_address', AddressBookService::get('presale'));
        $this->updateSetting('swap_address', AddressBookService::get('swap'));
        $this->updateSetting('token_address', AddressBookService::get('token'));
        $this->updateSetting('payment_address', AddressBookService::get('payment'));
        $this->updateSetting('vault_address', AddressBookService::get('vault'));
        $this->updateSetting('safe_address', AddressBookService::get('safe'));
        $this->updateSetting('autocompound_address', AddressBookService::get('autocompound'));
    }

    protected function updateSetting(string $name, string $value)
    {
        $setting = Setting::firstOrNew(['name' => $name]);
        $setting->value = $value;
        $setting->save();
    }
}
