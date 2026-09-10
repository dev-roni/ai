<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AiController extends Controller
{
    // চ্যাট পেজ দেখানো
    public function index()
    {
        return view('chat');
    }

}
