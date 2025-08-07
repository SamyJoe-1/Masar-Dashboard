<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test_1()
    {
        return view('test.1');
    }
}
