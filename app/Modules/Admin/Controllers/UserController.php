<?php

namespace App\Modules\Admin\Controllers;

use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function login()
    {
        return view('app');
    }
}
