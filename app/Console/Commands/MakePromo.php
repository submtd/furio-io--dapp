<?php

namespace App\Console\Commands;

use App\Models\Promo;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePromo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:promo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new promo';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $address = $this->ask('Enter the address');
        $max = $this->ask('Enter the maximum number of NFTs for this promo');
        if (filter_var($max, FILTER_VALIDATE_INT) === false) {
            $this->error("Max must be integer");
            return 1;
        }
        $price = $this->ask('Enter the price per NFT');
        if (filter_var($price, FILTER_VALIDATE_INT) === false) {
            $this->error("Price must be integer");
            return 1;
        }
        $value = $this->ask('Enter the value per NFT');
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->error("Value must be integer");
            return 1;
        }
        $promo = Promo::firstOrNew([
            'address' => $address,
        ]);
        $promo->max = $max;
        $promo->price = $price;
        $promo->value = $value;
        $promo->available = $max;
        $promo->save();
        $this->info('Promo created');
        $this->table([], [
            ['Address', $promo->address],
            ['Max', $promo->max],
            ['Price', $promo->price],
            ['Value', $promo->value],
        ]);
        return 0;
    }
}
