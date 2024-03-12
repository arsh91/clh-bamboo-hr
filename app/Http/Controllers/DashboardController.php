<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $usersCount = User::whereHas('role', function($q) {
            $q->where('name', '!=', 'SUPER_ADMIN');
        })->count();
        return view('dashboard.index',compact('usersCount'));
    }
}
