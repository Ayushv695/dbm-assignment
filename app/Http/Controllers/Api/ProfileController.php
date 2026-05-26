<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile()
    {
        return response()->json([
            'status' => true,
            'data' => auth()->user()
        ]);
    }
}
