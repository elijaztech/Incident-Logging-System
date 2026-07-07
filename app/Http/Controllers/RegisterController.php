<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    //store user registration in db
    public function store(Request $request)
    {
        //Validate form
        $validatedData = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phonenumber' => ['required', 'string'],
            'role'        => ['required', 'string'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'], // Checks password_confirmation automatically
        ]);

        // record to db table using User Eloquent Model
        $user = User::create([
            'name'        => $validatedData['name'],
            'email'       => $validatedData['email'],
            'phonenumber' => $validatedData['phonenumber'],
            'role'        => $validatedData['role'],
            // maybe encrypt later
            'password'    => Hash::make($validatedData['password']), 
        ]);

        // log in
        Auth::login($user);

        // redirect to the ticket logging system
        return redirect()->route('tickets.index')->with('success', 'Account registered successfully!');
    }
}