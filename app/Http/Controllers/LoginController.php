<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // page display
    public function showLoginForm()
    {
        return view('account.login'); 
    }

    // post
    public function login(Request $request)
    {
        // validate
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // login
        if (Auth::attempt($credentials)) {
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();
            
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'No account found with this email.']);
            }

            return redirect()->route('tickets.index');
        }

        //if login fails, send them back with an error message
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }
}