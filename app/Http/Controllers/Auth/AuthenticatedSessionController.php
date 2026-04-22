<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Contrôleur de session — connexion et déconnexion.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Authentifie l'utilisateur et crée la session.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Redirection selon le rôle
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', "Bienvenue dans l'administration, {$user->name} !");
        }

        if ($user->isClient()) {
            return redirect()->intended(route('user.favorites'))
                ->with('success', "Bienvenue, {$user->name} !");
        }

        return redirect()->intended(route('user.dashboard'))
            ->with('success', "Bienvenue, {$user->name} !");
    }

    /**
     * Détruit la session et redirige vers l'accueil.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
