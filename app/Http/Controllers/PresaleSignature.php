<?php

namespace App\Http\Controllers;

use App\Facades\Presale;
use App\Models\PresaleReservation;
use App\Services\SignerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PresaleSignature extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        if (!$address = Auth::user()) {
            abort(401, "Unauthenticated");
        }
        if (!$salt = Presale::getPresaleSalt()) {
            abort(404, "Not found");
        }
        if (Presale::getPresaleTotal() <= PresaleReservation::where('salt', $salt)->where('updated_at', '>', Carbon::now()->addMinutes(10))->sum('quantity')) {
            abort(422, "No presales available at the moment");
        }
        $expiration = Carbon::now()->addMinutes(10)->timestamp;
        $signature = SignerService::sign($address->address, $salt, $expiration);
        $presaleReservation = PresaleReservation::firstOrCreate([
            'address' => $address->address,
            'salt' => $salt,
        ]);
        $presaleReservation->quantity = $request->query('quantity');
        $presaleReservation->save();
        return response()->json([
            'salt' => $salt,
            'expiration' => $expiration,
            'signature' => $signature,
        ]);
    }
}
