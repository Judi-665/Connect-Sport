<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Club;
use App\Models\Sport;
use App\Models\Joueur;
use App\Models\Agent;
use App\Models\Supporter;
use App\Models\ParentJoueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ═══ Afficher formulaire inscription ═══
   // app/Http/Controllers/Auth/AuthController.php

public function showRegister()
{
    $clubs  = Club::orderBy('nom')->get(['id', 'nom', 'ville']);
    $sports = Sport::orderBy('id')->get(['id', 'nom']);
    
   

    return view('auth.register', compact('clubs', 'sports'));
}

    // ═══ Traiter inscription ═══
    public function register(Request $request)
    {
        $request->validate([
            'role'      => 'required|in:club,joueur,agent,supporter,parent',
            'email'     => 'required|email|lowercase|unique:users,email|max:150',
            'password'  => ['required', 'confirmed', Password::min(8)],
            'telephone' => 'nullable|string|max:20',
        ], [
            'role.required'      => 'Veuillez choisir votre profil.',
            'role.in'            => 'Profil invalide.',
            'email.required'     => 'L\'adresse email est obligatoire.',
            'email.email'        => 'L\'adresse email n\'est pas valide.',
            'email.unique'       => 'Cette adresse email est déjà utilisée.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // ── Construire name + prenom + telephone selon le rôle ──
        switch ($request->role) {
            case 'club':
                $name   = $request->input('club_nom') ?: 'Club';
                $prenom = null;
                $tel    = $request->input('club_telephone');
                break;
            case 'joueur':
                $name   = $request->input('nom_famille') ?: 'Joueur';
                $prenom = $request->input('prenom');
                $tel    = $request->input('telephone');
                break;
            case 'agent':
                $name   = $request->input('agent_nom') ?: 'Agent';
                $prenom = $request->input('agent_prenom');
                $tel    = $request->input('telephone');
                break;
            case 'supporter':
                $name   = $request->input('supporter_nom') ?: $request->input('supporter_prenom') ?: 'Supporter';
                $prenom = $request->input('supporter_prenom');
                $tel    = $request->input('telephone');
                break;
            case 'parent':
                $name   = $request->input('parent_nom') ?: 'Parent';
                $prenom = $request->input('parent_prenom');
                $tel    = $request->input('telephone');
                break;
            default:
                $name   = 'Utilisateur';
                $prenom = null;
                $tel    = null;
        }

        // ── Créer le compte User ──
        $user = User::create([
            'name'      => $name,
            'prenom'    => $prenom,
            'email'     => strtolower($request->email),
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'telephone' => $tel,
        ]);

        // ── Créer le profil lié selon le rôle ──
        switch ($request->role) {

            case 'club':
                Club::create([
                    'user_id'    => $user->id,
                    'sport_id'   => $request->input('club_sport_id'),
                    'nom'        => $request->input('club_nom', 'Club'),
                    'slug'       => Str::slug($request->input('club_nom', 'club') . '-' . $user->id),
                    'ville'      => $request->input('club_ville'),
                    'pays'       => $request->input('club_pays', 'Bénin'),
                    'telephone'  => $request->input('club_telephone'),
                    'abonnement' => 'gratuit',
                    'actif'      => 1,
                ]);
                break;

            case 'joueur':
                $clubId    = $request->input('club_id') ?: null;
                $sansClub  = $clubId ? 0 : 1;
                $disponible = $request->input('disponible', '1');

                Joueur::create([
                    'user_id'           => $user->id,
                    'club_id'           => $clubId,
                    'poste'             => $request->input('poste'),
                    'categorie'         => $request->input('categorie', 'senior'),
                    'date_naissance'    => $request->input('date_naissance') ?: null,
                    'nationalite'       => $request->input('pays'),
                    'telephone'         => $request->input('telephone'),
                    'pays'              => $request->input('pays', 'Bénin'),
                    'sans_club'         => $sansClub,
                    'visible_recruteur' => $disponible === '1' ? 1 : 0,
                    'actif'             => 1,
                ]);
                break;

            case 'agent':
                Agent::create([
                    'user_id'               => $user->id,
                    'numero_accreditation'  => $request->input('licence_agent') ?: null,
                    'pays'                  => $request->input('agent_pays', 'Bénin'),
                    'telephone'             => $request->input('telephone'),
                    'verifie'               => 0,
                    'actif'                 => 1,
                ]);
                break;

            case 'supporter':
                $supporter = Supporter::create([
                    'user_id' => $user->id,
                    'pays'    => $request->input('supporter_pays', 'Bénin'),
                    'actif'   => 1,
                ]);
                // Rattacher au club favori via table pivot club_supporter
                if ($request->filled('club_id')) {
                    $supporter->clubs()->attach($request->input('club_id'), [
                        'type_abonnement'        => 'gratuit',
                        'notifications_actives'  => 1,
                    ]);
                }
                break;

            case 'parent':
                ParentJoueur::create([
                    'user_id'      => $user->id,
                    'joueur_id'    => null, // lié depuis le dashboard après inscription
                    'lien'         => 'pere',
                    'acces_stats'  => 1,
                    'acces_agenda' => 1,
                    'actif'        => 1,
                ]);
                break;
        }

        return redirect()->route('login')
                         ->with('status', 'inscription_validee');
    }

    // ═══ Afficher formulaire connexion ═══
    public function showLogin()
    {
        return view('auth.login');
    }

    // ═══ Traiter connexion ═══
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.email'       => 'L\'adresse email n\'est pas valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email ou mot de passe incorrect.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(
            route($this->redirectParRole(Auth::user()->role))
        );
    }

    // ═══ Déconnexion ═══
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Déconnecté avec succès.');
    }

    // ═══ Redirection selon rôle ═══
    private function redirectParRole(string $role): string
    {
        return match($role) {
            'admin'     => 'admin.dashboard',
            'club'      => 'club.dashboard',
            'joueur'    => 'joueur.dashboard',
            'parent'    => 'parent.dashboard',
            'agent'     => 'agent.dashboard',
            'supporter' => 'supporter.dashboard',
            default     => 'home',
        };
    }
}