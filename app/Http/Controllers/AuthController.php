<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index()
    {
        $userId = request()->user()->id ?? null;
        if ($userId) {
            return redirect()->route('dashboard');
        } else {
            return view('login.index');
        }
    }

    public function login(Request $request)
	{
        if ($request->isMethod('get')) {
            $userId = request()->user()->id ?? null;
            if ($userId) {
                return redirect()->route('dashboard');
            } else {
                return view('login.index');
            }
        }
        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->intended('dashboard');
            }

            return back()->withErrors([
                'credentials_error' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

	}

	public function logOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('/');
    }
}
