<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceRecordsController extends Controller
{
    public function index()
    {
        return view('serviceRecords'); // blade file should be resources/views/serviceRecords.blade.php
    }
}
