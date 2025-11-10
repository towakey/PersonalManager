<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DebugController extends Controller
{
    public function checkPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['user_found' => false]);
        }

        $passwordCorrect = Hash::check($request->password, $user->password);

        return response()->json([
            'user_found' => true,
            'password_correct' => $passwordCorrect,
        ]);
    }
}
