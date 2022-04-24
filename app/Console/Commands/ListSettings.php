<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ListSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'list:settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all settings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $settings = [];
        foreach (Setting::orderBy('name')->get() as $setting) {
            $settings[$setting->name] = $setting->value;
        }
        $settings = array_merge($settings, config('settings', []));
        $rows = [];
        foreach ($settings as $name => $value) {
            $rows[] = [$name, Str::limit($value)];
        }
        $this->table(['Name', 'Value'], $rows);
        return 0;
    }
}
