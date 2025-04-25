<?php

namespace App\Http\Controllers;

use App\Services\GadgetService;
use App\Http\Requests\CatalogFilterRequest;

class GadgetController extends Controller
{
    public function index(CatalogFilterRequest $request, GadgetService $svc)
    {
        return view('gadgets.index', $svc->getCatalogPage($request->validated()));
    }

    public function show(string $slug, GadgetService $gadgetService)
    {
        $data = $gadgetService->getGadgetWithGroupedPrices($slug);
        return view('gadgets.show', $data);
    }
}
