<?php

namespace App\Http\Controllers;

use App\Http\Rules\ValidSignature;
use App\Models\Address;
use App\Models\PoolApr;
use App\Models\Promo;
use App\Services\SignerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdatePoolApr extends Controller
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
            'alltime' => 'required|numeric',
            'fourteenday' => 'required|numeric',
        ]);
        $address = Address::where('address', $request->get('address'))->first();
        if (!$address->admin) {
            abort(403, 'Unauthorized');
        }
        if (!$poolApr = PoolApr::first()) {
            $poolApr = new PoolApr;
        }
        $poolApr->update([
            'alltime' => $request->get('alltime'),
            'fourteenday' => $request->get('fourteenday'),
        ]);
        return response()->json(['success' => true]);
    }
}
