<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Club;
use App\Models\Joueur;
use App\Models\Sport;
use App\Models\Supporter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showRegister()
    {
        $sports = Sport::orderBy('id')->get(['id', 'nom']);
        $clubs = Club::actif()->orderBy('nom')->get(['id', 'nom', 'ville']);

        return view('auth.register', compact('clubs', 'sports'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|in:club,joueur,agent,supporter,parent',
            'email' => 'required|email|lowercase|unique:users,email|max:150',
            'password' => ['required', 'confirmed', Password::min(8)],
            'telephone' => 'nullable|string|max:20',
            'club_nom' => 'required_if:role,club|string|max:120',
            'club_sport_id' => 'required_if:role,club|exists:sports,id',
            'club_ville' => 'required_if:role,club|string|max:100',
            'club_pays' => 'nullable|string|max:80',
            'club_telephone' => 'nullable|string|max:20',
            'nom_famille' => 'required_if:role,joueur|string|max:100',
            'prenom' => 'required_if:role,joueur|string|max:100',
            'date_naissance' => 'nullable|date|before:today',
            'poste' => 'nullable|string|max:60',
            'categorie' => 'nullable|in:junior,cadet,senior,veteran',
            'pays' => 'nullable|string|max:80',
            'disponible' => 'nullable|in:0,1',
            'agent_nom' => 'required_if:role,agent|string|max:100',
            'agent_prenom' => 'nullable|string|max:100',
            'agent_pays' => 'nullable|string|max:100',
            'licence_agent' => 'nullable|string|max:100',
            'supporter_nom' => 'nullable|string|max:100',
            'supporter_prenom' => 'required_if:role,supporter|string|max:100',
            'supporter_pays' => 'nullable|string|max:100',
            'club_id' => 'nullable|exists:clubs,id',
            'parent_nom' => 'required_if:role,parent|string|max:100',
            'parent_prenom' => 'required_if:role,parent|string|max:100',
        ], [
            'role.required' => 'Veuillez choisir votre profil.',
            'role.in' => 'Profil invalide.',
            'email.required' => 'L’adresse email est obligatoire.',
            'email.email' => 'L’adresse email n’est pas valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        [$name, $prenom, $telephone] = match ($data['role']) {
            'club' => [$data['club_nom'], null, $data['club_telephone'] ?? null],
            'joueur' => [$data['nom_famille'], $data['prenom'], $data['telephone'] ?? null],
            'agent' => [$data['agent_nom'], $data['agent_prenom'] ?? null, $data['telephone'] ?? null],
            'supporter' => [($data['supporter_nom'] ?? '') ?: $data['supporter_prenom'], $data['supporter_prenom'], $data['telephone'] ?? null],
            'parent' => [$data['parent_nom'], $data['parent_prenom'], $data['telephone'] ?? null],
        };

        DB::transaction(function () use ($data, $name, $prenom, $telephone): void {
            $user = User::create([
                'name' => $name,
                'prenom' => $prenom,
                'email' => Str::lower($data['email']),
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'telephone' => $telephone,
            ]);

            switch ($data['role']) {
                case 'club':
                    Club::create([
                        'user_id' => $user->id,
                        'sport_id' => $data['club_sport_id'],
                        'nom' => $data['club_nom'],
                        'slug' => Str::slug($data['club_nom'] . '-' . $user->id),
                        'ville' => $data['club_ville'],
                        'pays' => $data['club_pays'] ?? 'Bénin',
                        'telephone' => $data['club_telephone'] ?? null,
                        'abonnement' => 'gratuit',
                        'actif' => true,
                    ]);
                    break;

                case 'joueur':
                    Joueur::create([
                        'user_id' => $user->id,
                        'club_id' => null,
                        'poste' => $data['poste'] ?? null,
                        'categorie' => $data['categorie'] ?? 'senior',
                        'date_naissance' => $data['date_naissance'] ?? null,
                        'nationalite' => $data['pays'] ?? null,
                        'telephone' => $data['telephone'] ?? null,
                        'pays' => $data['pays'] ?? 'Bénin',
                        'sans_club' => true,
                        'visible_recruteur' => ($data['disponible'] ?? '1') === '1',
                        'actif' => true,
                    ]);
                    break;

                case 'agent':
                    Agent::create([
                        'user_id' => $user->id,
                        'numero_accreditation' => $data['licence_agent'] ?? null,
                        'pays' => $data['agent_pays'] ?? 'Bénin',
                        'telephone' => $data['telephone'] ?? null,
                        'verifie' => false,
                        'actif' => true,
                    ]);
                    break;

                case 'supporter':
                    $supporter = Supporter::create([
                        'user_id' => $user->id,
                        'pays' => $data['supporter_pays'] ?? 'Bénin',
                        'actif' => true,
                    ]);
                    if (!empty($data['club_id'])) {
                        $supporter->clubs()->attach($data['club_id'], [
                            'type_abonnement' => 'gratuit',
                            'notifications_actives' => true,
                        ]);
                    }
                    break;

                case 'parent':
                    // Les liens parent-joueur sont créés sur demande et confirmés par le joueur.
                    break;
            }
        });

        return redirect()->route('login')->with('status', 'inscription_validee');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'L’adresse email est obligatoire.',
            'email.email' => 'L’adresse email n’est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        $credentials['email'] = Str::lower($credentials['email']);
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email ou mot de passe incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($this->redirectParRole(Auth::user()->role)));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Déconnecté avec succès.');
    }

    private function redirectParRole(string $role): string
    {
        return match ($role) {
            'admin' => 'admin.dashboard',
            'club' => 'club.dashboard',
            'joueur' => 'joueur.dashboard',
            'parent' => 'parent.dashboard',
            'agent' => 'agent.dashboard',
            'supporter' => 'supporter.dashboard',
            default => 'home',
        };
    }
}
