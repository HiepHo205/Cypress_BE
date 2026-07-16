<?php

namespace App\Modules\Admin\Controllers;

use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin::index');
    }

    public function login()
    {
        return view('login');
    }
}
