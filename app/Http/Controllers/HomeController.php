<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function home()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard.hr.home');
        }else{
            return redirect('/');
        }
    }

    public function dashboard()
    {
        return view('dashboard.hr.home');
    }
}
