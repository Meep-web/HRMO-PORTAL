<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Town;
use App\Models\Barangay;

class LocationController extends Controller
{
    public function getProvinces()
    {
        return response()->json(Province::all());
    }

    public function getTowns($provinceCode)
    {
        return response()->json(Town::where('provinceCode', $provinceCode)->get());
    }

    public function getBarangays($townCode)
    {
        return response()->json(Barangay::where('townCode', $townCode)->get());
    }
}
