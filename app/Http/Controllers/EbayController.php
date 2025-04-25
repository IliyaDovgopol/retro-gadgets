<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EbayService;

class EbayController extends Controller
{
    public function getEbayItems(EbayService $ebayService)
	{
		$items = $ebayService->getFilteredItems('retro phone');
		return response()->json($items, 200, [], JSON_PRETTY_PRINT);
	}

}
