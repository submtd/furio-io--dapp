<?php

namespace App\Http\Controllers;

use App\Http\Rules\ValidSignature;
use App\Models\Promo;
use App\Services\SignerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GetPromo extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'address' => 'required|exists:addresses,address',
            'nonce' => 'required',
            'signature' => [
                'required',
                new ValidSignature,
            ],
        ]);
        if (!$promo = Promo::where('address', $request->get('address'))->where('available', '>', 0)->first()) {
            return response()->json([
                'available' => false,
            ]);
        }
        $salt = implode('', [
            'max', $promo->max,
            'price', $promo->price,
            'value', $promo->value,
            'total', 1000000,
        ]);
        $expiration = Carbon::now()->addCentury()->timestamp;
        $signature = SignerService::sign($request->get('address'), $salt, $expiration);
        return response()->json([
            'available' => true,
            'max' => $promo->max,
            'price' => $promo->price,
            'value' => $promo->value,
            'total' => 1000000,
            'salt' => $salt,
            'expiration' => $expiration,
            'signature' => $signature,
        ]);
    }
}
