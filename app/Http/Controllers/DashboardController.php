<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * ダッシュボードを表示
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('dashboard');
    }
}
