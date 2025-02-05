<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PersonalDataSheetController extends Controller
{
    public function index()
    {
        return view('personal-data-sheet');
    }
}