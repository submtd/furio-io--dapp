<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Routing\Controller;

class GetTeam extends Controller
{
    /**
     * Invoke.
     *
     * @param $team - Name of team
     */
    public function __invoke($team)
    {
        if (!$address = Address::where('name', $team)->first()) {
            abort(404);
        }

        return AddressResource::make($address);
    }
}
