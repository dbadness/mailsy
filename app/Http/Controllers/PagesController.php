<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;

class PagesController extends Controller
{
    public function __construct()
    {
        // everything in this controller is for authed users only
        $this->middleware('auth');
    }

    // show the home page once the user is authed
    public function showHome()
    {
        $user = Auth::user();
        return view('pages.home', ['user' => $user]);
    }

    // the email creation page
    public function showNewEmail()
    {
        $user = Auth::user();
        return view('pages.newemail', ['user' => $user]);
    }
}
