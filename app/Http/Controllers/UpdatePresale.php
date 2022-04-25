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

class UpdatePresale extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        if (!$address = Auth::user()) {
            abort(401, "Unauthenticated");
        }
        $salt = implode('', [
            'max', $request->query('max'),
            'price', $request->query('price'),
            'value', $request->query('value'),
            'total', $request->query('total'),
        ]);
        PresaleReservation::where('address', $address->address)->where('salt', $salt)->delete();
        return response()->json([]);
    }
}
