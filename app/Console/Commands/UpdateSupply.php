<?php

namespace App\Console\Commands;

use App\Models\Price;
use App\Models\Setting;
use App\Services\AddressBookService;
use App\Services\TokenService;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateSupply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:supply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the current $FUR supply.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $totalSupply = TokenService::totalSupply();
        $circulatingSupply = bcsub($totalSupply, TokenService::balanceOf(AddressBookService::get('vault')));
        $this->updateSetting('total_supply', $totalSupply);
        $this->updateSetting('circulating_supply', $circulatingSupply);
    }

    protected function updateSetting(string $name, string $value)
    {
        $setting = Setting::firstOrNew(['name' => $name]);
        $setting->value = $value;
        $setting->save();
    }
}
