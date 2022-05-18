<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    /**
     * Get settings
     *
     * @return array
     */
    public static function get(): array
    {
        $settings = [];
        foreach (Setting::orderBy('name')->get() as $setting) {
            $settings[$setting->name] = $setting->value;
        }
        $settings = array_merge($settings, config('settings', []));
        $settings['server_time'] = now()->timestamp;

        return $settings;
    }
}
