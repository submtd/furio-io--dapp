<?php

namespace Database\Seeders;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            'network_id' => 56,
            'network_name' => 'Binance Smart Chain',
            'recaptcha' => '6LdxfMweAAAAACFtJU-1PDJCYqtRhYpWDZxq1nMJ',
            'presale_one_start' => Carbon::now()->addMinutes(10)->timestamp,
            'presale_one_max' => 1,
            'presale_one_price' => 250,
            'presale_one_value' => 500,
            'presale_one_total' => 300,
            'show_presale_one_timer' => true,
            'presale_two_start' => Carbon::now()->addMinutes(15)->timestamp,
            'presale_two_max' => 10,
            'presale_two_price' => 150,
            'presale_two_value' => 100,
            'presale_two_total' => 1250,
            'show_presale_two_timer' => true,
            'presale_three_start' => Carbon::now()->addMinutes(20)->timestamp,
            'presale_three_max' => 10,
            'presale_three_price' => 175,
            'presale_three_value' => 100,
            'presale_three_total' => 1250,
            'show_presale_three_timer' => true,
            'claim_start' => Carbon::now()->addMinutes(30)->timestamp,
            'show_claim_timer' => true,
            'rpc_url' => 'https://bsc-dataseed.binance.org/',
            'wss_url' => 'wss://ws-nd-333-373-227.p2pify.com/baa0f3b103404d63c2855df68f8b755a',
            'token_decimals' => 18,
            'payment_decimals' => 6,
        ];
        foreach ($settings as $name => $value) {
            $settingModel = Setting::firstOrNew([
                'name' => $name,
            ]);
            $settingModel->value = $value;
            $settingModel->save();
        }
    }
}
