<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Web3\Utils;

class GetAddress extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke($address)
    {
        if (!Utils::isAddress($address)) {
            abort(422, "Invalid address");
        }
        $address = Address::firstOrNew([
            'address' => $address,
        ]);
        if (!$address->nonce) {
            $address->nonce = 'FURIO-MESSAGE-VALIDATION-' . Str::random(8);
        }
        $address->save();

        return AddressResource::make($address);
    }
}
