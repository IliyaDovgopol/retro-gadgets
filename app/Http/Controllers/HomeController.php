<?php

namespace App\Http\Controllers;

use App\Services\GadgetService;

class HomeController extends Controller
{
    public function index(GadgetService $svc)
	{
		$gadgets = $svc->getRandomGadgetsForHomepage(8);
		return view('home', compact('gadgets'));
	}
}
