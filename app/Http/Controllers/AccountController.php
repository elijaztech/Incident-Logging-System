<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    //show the profile edit form
    public function edit()
    {
        $user = auth()->user();
        return view('account.manage', compact('user'));
    }

    //update the profile data in the database
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        //only update password if the user typed a new one
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Account profile successfully updated!');
    }
    public function destroy(Request $request)
    {
        $user = auth()->user();

        //logout
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        //deletefromdb
        $user->delete();

        //back to login
        return redirect('/login')->with('success', 'Your account has been successfully deleted.');
    }
}