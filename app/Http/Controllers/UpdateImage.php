<?php

namespace App\Http\Controllers;

use App\Http\Resources\AddressResource;
use App\Http\Rules\ValidSignature;
use App\Models\Address;
use App\Models\Avatar;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class UpdateImage extends Controller
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
            'image' => 'nullable|mimes:jpg,jpeg,png,svg|max:2048'
        ]);
        if(!$address = Address::where('address', $request->get('address'))->first()) {
            abort(404);
        }
        $file = $request->file('image');
        $fileName = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $filePath = $request->file('image')->storeAs('uploads', $fileName, 'public');
        if (!$avatar = $address->avatar) {
            $avatar = new Avatar();
        }
        $avatar->address_id = $address->id;
        $avatar->name = $fileName;
        $avatar->path = $filePath;
        $avatar->save();

        return AddressResource::make($address);
    }
}
