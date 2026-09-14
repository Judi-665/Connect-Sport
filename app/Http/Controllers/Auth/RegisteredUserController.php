<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'role'      => ['required', 'in:club,joueur,agent,supporter,parent'],
            'email'     => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'telephone' => ['nullable', 'string', 'max:20'],
        ], [
            'role.required'      => 'Veuillez choisir votre profil.',
            'role.in'            => 'Profil invalide.',
            'email.required'     => 'L\'adresse email est obligatoire.',
            'email.email'        => 'L\'adresse email n\'est pas valide.',
            'email.unique'       => 'Cette adresse email est déjà utilisée.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // Construire name et prenom selon le rôle
        switch ($request->role) {
            case 'club':
                $name   = $request->input('club_nom', 'Club');
                $prenom = null;
                $tel    = $request->input('club_telephone');
                break;
            case 'joueur':
                $name   = $request->input('name', $request->input('nom_famille', 'Joueur'));
                $prenom = $request->input('prenom');
                $tel    = $request->input('telephone');
                break;
            case 'agent':
                $name   = $request->input('agent_nom', 'Agent');
                $prenom = $request->input('agent_prenom');
                $tel    = $request->input('telephone');
                break;
            case 'supporter':
                $name   = $request->input('name', $request->input('supporter_prenom', 'Supporter'));
                $prenom = $request->input('supporter_prenom');
                $tel    = $request->input('telephone');
                break;
            case 'parent':
                $name   = $request->input('parent_nom', 'Parent');
                $prenom = $request->input('parent_prenom');
                $tel    = $request->input('telephone');
                break;
            default:
                $name   = $request->input('name', 'Utilisateur');
                $prenom = null;
                $tel    = null;
        }

        $user = User::create([
            'name'      => $name,
            'prenom'    => $prenom,
            'email'     => strtolower($request->email),
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'telephone' => $tel,
        ]);

        event(new Registered($user));

        return redirect()->route('login')
            ->with('status', 'inscription_validee');
    }
}