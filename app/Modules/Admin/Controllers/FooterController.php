<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Services\Layout\Footer\FooterService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FooterController extends Controller
{
    public function __construct(
        protected FooterService $service,
    ) {}

    public function index()
    {
        return view('app');
    }
}
