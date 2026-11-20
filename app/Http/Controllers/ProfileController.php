<?php

namespace App\Http\Controllers;

use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['profile' => User::orderBy('id')->first()]);
    }
}
