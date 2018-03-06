<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

require_once base_path('config/clubhouse.php');

class ConfigController extends Controller
{
    public function show() {
        global $configValues;
        return response()->json($configValues);
    }
}
