<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function upload()
    {
        return view('pages.upload');
    }
}
