<?php
// app/Http/Controllers/Supporter/SupporterController.php

namespace App\Http\Controllers\Supporter;

use App\Http\Controllers\Controller;
use App\Models\Supporter;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupporterController extends Controller
{
    // ═══ Dashboard supporter ═══
    public function dashboard()
    {
        $supporter = Auth::user()->supporter;

        if (!$supporter) {
            return redirect()->route('supporter.create')
                             ->with('info', 'Veuillez créer votre profil supporter');
        }

        $clubsFollow = $supporter->clubs()->paginate(10);
        $stats = [
            'clubs_follow' => $supporter->clubs()->count(),
            'premium'      => $supporter->clubsPremium()->count(),
        ];

        return view('supporter.dashboard', compact('supporter', 'clubsFollow', 'stats'));
    }

    // ═══ Créer profil ═══
    public function create()
    {
        return view('supporter.create');
    }

    // ═══ Enregistrer profil ═══
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'ville' => 'required|string|max:100',
            'pays'  => 'required|string|max:100',
            'bio'   => 'nullable|string|max:500',
        ]);

        Supporter::create(array_merge($validated, [
            'user_id' => $user->id,
            'actif'   => true,
        ]));

        return redirect()->route('supporter.dashboard')
                        ->with('success', 'Profil créé');
    }

    // ═══ Profil supporter ═══
    public function profil()
    {
        $supporter = Auth::user()->supporter;
        return view('supporter.profil', compact('supporter'));
    }

    // ═══ Éditer profil ═══
    public function edit()
    {
        $supporter = Auth::user()->supporter;
        return view('supporter.edit', compact('supporter'));
    }

    // ═══ Mettre à jour ═══
    public function update(Request $request)
    {
        $supporter = Auth::user()->supporter;

        $validated = $request->validate([
            'ville' => 'required|string|max:100',
            'pays'  => 'required|string|max:100',
            'bio'   => 'nullable|string|max:500',
        ]);

        $supporter->update($validated);

        return redirect()->route('supporter.profil')
                        ->with('success', 'Profil mis à jour');
    }

    // ═══ Suivre un club ═══
    public function followClub(Request $request)
    {
        $supporter = Auth::user()->supporter;

        $validated = $request->validate([
            'club_id'               => 'required|exists:clubs,id',
            'type_abonnement'       => 'required|in:gratuit,premium',
            'notifications_actives' => 'sometimes|boolean',
        ]);

        $existant = $supporter->clubs()
                             ->where('club_id', $validated['club_id'])
                             ->exists();

        if ($existant) {
            return back()->with('warning', 'Vous suivez déjà ce club');
        }

        $supporter->clubs()->attach($validated['club_id'], [
            'type_abonnement'       => $validated['type_abonnement'],
            'notifications_actives' => $validated['notifications_actives'] ?? true,
            'abonnement_expire_at'  => now()->addYear(),
        ]);

        return back()->with('success', 'Club suivi');
    }

    // ═══ Clubs suivis ═══
    public function clubsSuivis()
    {
        $supporter = Auth::user()->supporter;

        $clubs = $supporter->clubs()
                          ->withPivot('type_abonnement', 'abonnement_expire_at')
                          ->paginate(15);

        return view('supporter.clubs-suivis', compact('clubs'));
    }

    // ═══ Ne plus suivre ═══
    public function unfollowClub(Club $club)
    {
        $supporter = Auth::user()->supporter;
        $supporter->clubs()->detach($club->id);

        return back()->with('success', 'Club suivi supprimé');
    }

    // ═══ Renouveller abonnement premium ═══
    public function renewPremium(Request $request, Club $club)
    {
        $supporter = Auth::user()->supporter;

        // Intégration paiement FedaPay
        return redirect()->route('paiement.fedapay.abonnement', [
            'club_id' => $club->id,
            'type'    => 'supporter',
        ]);
    }
}
