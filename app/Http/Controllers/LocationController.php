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
    // Sorting provinces alphabetically by name
    return response()->json(Province::orderBy('provinceName', 'asc')->get());
}

public function getTowns($provinceCode)
{
    // Sorting towns alphabetically by name
    return response()->json(Town::where('provinceCode', $provinceCode)
                                 ->orderBy('townName', 'asc')
                                 ->get());
}

public function getBarangays($townCode)
{
    // Sorting barangays alphabetically by name
    return response()->json(Barangay::where('townCode', $townCode)
                                    ->orderBy('barangayName', 'asc')
                                    ->get());
}

}