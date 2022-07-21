<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdateVault extends Controller
{
    /**
     * Invoke.
     *
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        if (!$request->has('address')) {
            return response()->json([]);
        }
        if (!$address = Address::where('address', $request->get('address'))->first()) {
            return response()->json([]);
        }
        $vault = $address->vault()->firstOrNew([
            'address_id' => $address->id,
        ]);
        $vault->fill([
            'start_time' => Carbon::createFromTimestamp($request->get('start_time') ?? $vault->start_time),
            'balance' => $request->get('balance') ?? $vault->balance,
            'deposited' => $request->get('deposited') ?? $vault->deposited,
            'compounded' => $request->get('compounded') ?? $vault->compounded,
            'claimed' => $request->get('claimed') ?? $vault->claimed,
            'taxed' => $request->get('taxed') ?? $vault->taxed,
            'awarded' => $request->get('awarded') ?? $vault->awarded,
            'negative' => $request->get('negative') ?? $vault->negative,
            'penalized' => $request->get('penalized') ?? $vault->penalized,
            'maxed' => $request->get('maxed') ?? $vault->maxed,
            'banned' => $request->get('banned') ?? $vault->banned,
            'team_wallet' => $request->get('team_wallet') ?? $vault->team_wallet,
            'complete' => $request->get('complete') ?? $vault->complete,
            'maxed_rate' => $request->get('maxed_rate') ?? $vault->maxed_rate,
            'direct_referrals' => $request->get('direct_referrals') ?? $vault->direct_referrals,
            'airdrop_sent' => $request->get('airdrop_sent') ?? $vault->airdrop_sent,
            'airdrop_received' => $request->get('airdrop_received') ?? $vault->airdrop_received,
        ]);
        if ($vault->isDirty()) {
            $vault->save();
        }
        return response()->json([]);
    }
}
