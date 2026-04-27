<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Villa;

class HomeController extends Controller
{
    public function index()
    {
        $villas = Villa::where('status', 'active')
            ->select(
                'id',
                'name',
                'location',
                'weekday_price',
                'weekend_price',
                'images'
            )
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('villas'));
    }
}