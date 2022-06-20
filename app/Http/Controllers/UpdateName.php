<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Http\Rules\ValidSignature;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdateName extends Controller
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
            'name' => 'nullable|max:255',
            //'name' => 'nullable|max:255|unique:addresses,name,'.$request->get('address').',address',
        ]);
        $address = Address::where('address', $request->get('address'))->first();
        $address->name = $request->get('name');
        $address->save();

        return AddressResource::make($address);
    }
}
