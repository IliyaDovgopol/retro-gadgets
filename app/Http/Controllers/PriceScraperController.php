<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GadgetParsersService;
use App\Models\Gadget;

class PriceScraperController extends Controller
{
    public function fetch(Request $request, GadgetParsersService $parser)
    {
        $gadget = Gadget::findOrFail($request->input('id'));

        $prom = $parser->updateFromProm($gadget->name);
        $olx = $parser->updateFromOlx($gadget->name);

        return response()->json([
            'prom' => $prom,
            'olx' => $olx,
        ]);
    }
}
