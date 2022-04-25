<?php

namespace App\Http\Controllers;

use App\Facades\Presale;
use App\Models\Promo;
use App\Services\SignerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class GetPromo extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        if (!$address = Auth::user()) {
            abort(401, "Unauthenticated");
        }
        if (!$promo = Promo::where('address', $address->address)->where('available', '>', 0)->first()) {
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
        $signature = SignerService::sign($address->address, $salt, $expiration);
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
