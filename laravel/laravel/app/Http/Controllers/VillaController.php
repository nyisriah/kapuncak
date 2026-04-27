<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Villa;

class VillaController extends Controller
{
    public function index(Request $request)
    {
        $query = Villa::where('status', 'active');

        // 🔍 FILTER
        if ($request->min_price) {
            $query->where('weekday_price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('weekday_price', '<=', $request->max_price);
        }

        if ($request->bedrooms) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        if ($request->guests) {
            $query->where('max_guests', '>=', $request->guests);
        }

        // 📄 PAGINATION
        $villas = $query->latest()->paginate(9)->withQueryString();

        return view('villas.index', compact('villas'));
    }

    public function show(Villa $villa)
    {
        return view('villas.show', compact('villa'));
    }
}