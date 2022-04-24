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
            [
                'name' => 'network_id',
                'value' => 56,
            ],
            [
                'name' => 'network_name',
                'value' => 'Binance Smart Chain',
            ],
            [
                'name' => 'recaptcha',
                'value' => '6LdxfMweAAAAACFtJU-1PDJCYqtRhYpWDZxq1nMJ',
            ],
            [
                'name' => 'presale_one_start',
                'value' => Carbon::now()->addMinutes(5)->timestamp,
            ],
            [
                'name' => 'presale_one_max',
                'value' => 1,
            ],
            [
                'name' => 'presale_one_price',
                'value' => 250,
            ],
            [
                'name' => 'presale_one_value',
                'value' => 500,
            ],
            [
                'name' => 'presale_two_start',
                'value' => Carbon::now()->addMinutes(10)->timestamp,
            ],
            [
                'name' => 'presale_two_max',
                'value' => 10,
            ],
            [
                'name' => 'presale_two_price',
                'value' => 150,
            ],
            [
                'name' => 'presale_two_value',
                'value' => 100,
            ],
            [
                'name' => 'presale_three_start',
                'value' => Carbon::now()->addMinutes(15)->timestamp,
            ],
            [
                'name' => 'presale_three_max',
                'value' => 10,
            ],
            [
                'name' => 'presale_three_price',
                'value' => 175,
            ],
            [
                'name' => 'presale_three_value',
                'value' => 100,
            ],
            [
                'name' => 'claim_start',
                'value' => Carbon::now()->addMinutes(20)->timestamp,
            ],
            [
                'name' => 'show_presale_one_timer',
                'value' => true,
            ],
            [
                'name' => 'show_presale_two_timer',
                'value' => true,
            ],
            [
                'name' => 'show_presale_three_timer',
                'value' => true,
            ],
            [
                'name' => 'show_claim_timer',
                'value' => true,
            ],
            [
                'name' => 'rpc_url',
                'value' => 'https://nd-333-373-227.p2pify.com/baa0f3b103404d63c2855df68f8b755a',
            ],
            [
                'name' => 'wss_url',
                'value' => 'wss://ws-nd-333-373-227.p2pify.com/baa0f3b103404d63c2855df68f8b755a',
            ],
            [
                'name' => 'token_decimals',
                'value' => 18,
            ],
            [
                'name' => 'payment_decimals',
                'value' => 6,
            ],
        ];
        foreach ($settings as $setting) {
            $settingModel = Setting::firstOrNew([
                'name' => $setting['name'],
            ]);
            $settingModel->value = $setting['value'];
            $settingModel->save();
        }
    }
}
