<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('selfbuy');
    }

    public function about()
    {
        return view('partials.about_us');
    }

    public function faq()
    {
        return view('partials.faq');
    }

    public function contact()
    {
        return view('partials.contact');
    }

    public function payment()
    {
        return view('partials.payment');
    }

    public function money_back_guarantee()
    {
        return view('partials.money_back');
    }

    public function refund_policy()
    {
        return view('partials.refund_policy');
    }

    public function shipping()
    {
        return view('partials.shipping');
    }

    public function terms_and_conditions()
    {
        return view('partials.terms_condition');
    }

    public function privacy_policy()
    {
        return view('partials.privacy_policy');
    }

    public function track_my_order()
    {
        return view('partials.track_my_order');
    }

    public function blog()
    {
        return view('partials.blog');
    }

}
