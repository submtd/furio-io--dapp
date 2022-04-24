<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new setting';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->ask('Enter the setting name');
        $value = $this->ask('Enter the setting value');
        $name = Str::snake($name);
        $setting = Setting::firstOrNew([
            'name' => $name,
        ]);
        $setting->value = $value;
        $setting->save();
        $this->info('Setting created');
        $this->table(['Name', 'Value'], [[$name, Str::limit($value)]]);
        return 0;
    }
}
