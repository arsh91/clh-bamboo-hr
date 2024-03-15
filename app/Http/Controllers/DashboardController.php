<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        //Count of Users Without Super Admin
        $usersCount = User::whereHas('role', function($q) {
            $q->where('name', '!=', 'SUPER_ADMIN');
        })->count();

        
        //Count of Catalog 
        $catalogCount = Catalog::count();

        return view('dashboard.index',compact('usersCount','catalogCount'));
    }
}
