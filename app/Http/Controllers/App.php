<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;

class App extends Controller
{
    /**
     * Invoke.
     *
     * @param Request $request
     */
    public function __invoke(Request $request)
    {
        if ($referrer = $request->query('ref') ?? $request->session()->get('ref') ?? Cookie::get('ref')) {
            $request->session()->put('ref', $referrer);
            Cookie::queue('ref', $referrer, 43200);
        }

        return view('app');
    }
}
