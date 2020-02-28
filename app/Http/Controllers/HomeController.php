<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function paypal()
    {
        return view('paypal');
    }

    public function stripe()
    {
        return view('stripe');
    }
}
