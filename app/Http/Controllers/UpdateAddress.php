<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
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
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|max:255',
            'email_verification_code' => 'nullable',
        ]);
        $address = Address::firstOrNew([
            'address' => $request->get('address'),
        ]);
        if ($email = $request->get('email')) {
            if ($address->email != $email) {
                if (Address::where('email', $email)->count() != 0) {
                    return response()->json([
                        'error' => true,
                        'message' => 'Email has already been registered',
                    ]);
                }
                $address->email = $email;
                $address->email_verification_code = strtoupper(Str::random(8));
                // SEND VERIFICATION EMAIL
                Mail::to($address->email)->send(new VerifyEmail($address->email_verification_code));
            }
        }
        if ($code = $request->get('email_verification_code')) {
            if (strtoupper($code) == strtoupper($address->email_verification_code)) {
                $address->email_verified_at = Carbon::now();
            }
        }
        if ($name = $request->get('name')) {
            $address->name = $name;
        }
        $address->nonce = Str::random(32);
        $address->save();

        return response()->json([
            'nonce' => $address->nonce,
            'name' => $address->name,
            'email' => $address->email,
            'email_verified_at' => $address->email_verified_at,
        ]);
    }
}
