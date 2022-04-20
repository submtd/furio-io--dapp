<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class Settings extends Controller
{
    /**
     * Invoke.
     */
    public function __invoke(Request $request)
    {
        return response()->json(config('settings'));
    }
}
