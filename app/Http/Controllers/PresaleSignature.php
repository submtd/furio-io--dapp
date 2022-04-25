<?php

namespace App\Http\Controllers;

use App\Facades\Presale;
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
        $expiration = Carbon::now()->addMinutes(10)->timestamp;
        $signature = SignerService::sign($address->address, $salt, $expiration);
        Cache::increment($salt.'_reservations', $request->query('quantity'));
        return response()->json([
            'salt' => $salt,
            'expiration' => $expiration,
            'signature' => $signature,
        ]);
    }
}
