<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Avatar;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UpdateAddress extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'name' => 'nullable|max:255|unique:addresses,name,'.$request->get('address').',address',
            'avatar' => 'nullable|mimes:jpg,jpeg,png|max:2048',
        ]);
        $address = Address::firstOrNew([
            'address' => $request->get('address'),
        ]);
        $address->nonce = 'FURIO-MESSAGE-VALIDATION-' . Str::random(8);
        if (Auth::user() && $name = $request->get('name')) {
            $address->name = $name;
        }
        $address->save();
        if (Auth::user() && $file = $request->file('avatar')) {
            $fileName = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
            $filePath = $request->file('avatar')->storeAs('uploads', $fileName, 'public');
            if (!$avatar = $address->avatar) {
                $avatar = new Avatar();
            }
            $avatar->address_id = $address->id;
            $avatar->name = $fileName;
            $avatar->path = $filePath;
            $avatar->save();
        }

        return response()->json([
            'nonce' => $address->nonce,
            'name' => $address->name,
        ]);
    }
}
