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
use Illuminate\View\View;

/**
 * Contrôleur d'inscription — surcharge Breeze pour gérer le champ 'role'
 * et le numéro de téléphone.
 */
class RegisteredUserController extends Controller
{
    /**
     * Affiche le formulaire d'inscription.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Valide et enregistre un nouvel utilisateur.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:'.User::class],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:particulier,agent,client'],  // 'admin' non accessible à l'inscription
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($user->isClient()) {
            return redirect()->route('home')
                ->with('success', "Bienvenue sur Sarouty, {$user->name} ! Votre compte a été créé avec succès.");
        }

        return redirect()->route('user.dashboard')
            ->with('success', "Bienvenue sur Sarouty, {$user->name} ! Votre compte a été créé avec succès.");
    }
}
