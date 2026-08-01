<?php

namespace App\Modules\Admin\Controllers;

use App\Core\Services\Layout\Header\HeaderService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class HeaderController extends Controller
{
    public function __construct(
        protected HeaderService $headerService
    ) {}


    public function index()
    {
        return view('app');
    }


    public function data()
    {
        return response()->json(
            $this->headerService->getHeader()
        );
    }


    public function store(Request $request)
    {
        return response()->json(
            $this->headerService->createMenu(
                $request->all()
            )
        );
    }


    public function update(Request $request, int $id)
    {
        return response()->json(
            $this->headerService->updateMenu(
                $id,
                $request->all()
            )
        );
    }


    public function destroy(int $id)
    {
        return response()->json(
            $this->headerService->deleteMenu($id)
        );
    }
}
