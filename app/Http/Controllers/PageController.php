<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function privacy()
    {
        $this->data['title'] = 'Privacy Policy';
        return view('pagePrivacyPolicy', $this->data);
    }

    public function terms()
    {
        $this->data['title'] = 'Terms And Conditions';
        return view('pageTermsAndConditions', $this->data);
    }
}
