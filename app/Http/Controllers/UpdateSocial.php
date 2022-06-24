<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Http\Rules\ValidSignature;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UpdateSocial extends Controller
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
            'twitter' => 'nullable|max:255|url',
            'telegram' => 'nullable|max:255|url',
            'discord' => 'nullable|max:255|url',
            'medium' => 'nullable|max:255|url',
            'facebook' => 'nullable|max:255|url',
            'instagram' => 'nullable|max:255|url',
        ]);
        if(!$address = Address::where('address', $request->get('address'))->first()) {
            abort(404);
        }
        $address->fill([
            'twitter' => $request->get('twitter'),
            'telegram' => $request->get('telegram'),
            'discord' => $request->get('discord'),
            'medium' => $request->get('medium'),
            'facebook' => $request->get('facebook'),
            'instagram' => $request->get('instagram'),
        ]);
        $address->save();

        return AddressResource::make($address);
    }
}
