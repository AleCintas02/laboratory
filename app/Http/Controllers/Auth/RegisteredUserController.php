<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'dni' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'rol' => 'string|in:root,A,P', // quita el 'required' si deseas el valor por defecto
        ]);
    
        $user = User::create([
            'dni' => $request->dni,
            'last_name' => $request->last_name,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol ?? 'P', // asigna 'P' si no se proporciona otro valor
        ]);
    
        event(new Registered($user));
    
        Auth::login($user);
    
        return redirect(route('dashboard', absolute: false));
    }
    
}
