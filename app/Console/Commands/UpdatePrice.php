<?php

namespace App\Console\Commands;

use App\Models\Price;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdatePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the current $FUR price.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.pancakeswap.info/api/v2/tokens/0x48378891d6E459ca9a56B88b406E8F4eAB2e39bF');
        $priceData = json_decode($response->getBody()->getContents());
        $price = Price::create([
            'price' => $priceData->data->price,
        ]);
        $this->info($price->price);
    }
}
