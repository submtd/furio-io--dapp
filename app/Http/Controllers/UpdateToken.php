<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdateToken extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'total_supply' => 'required|numeric',
            'circulating_supply' => 'required|numeric',
        ]);
        $totalSupply = Setting::firstOrNew([
            'name' => 'total_supply',
        ]);
        $totalSupply->value = $request->get('total_supply');
        $totalSupply->save();
        $circulatingSupply = Setting::firstOrNew([
            'name' => 'circulating_supply',
        ]);
        $circulatingSupply->value = $request->get('circulating_supply');
        $circulatingSupply->save();
        $lastTokenUpdate = Setting::firstOrNew([
            'name' => 'last_token_update',
        ]);
        $lastTokenUpdate->value = Carbon::now()->timestamp * 1000;
        $lastTokenUpdate->save();

        return response()->json([]);
    }
}
