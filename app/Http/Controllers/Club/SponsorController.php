<?php
// app/Http/Controllers/Club/SponsorController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SponsorController extends Controller
{
    // ═══ Lister les sponsors ═══
    public function index(Request $request)
{
    $club  = Auth::user()->club;
    $query = $club->sponsors()->orderBy('created_at', 'desc');

    if ($request->filled('visibilite')) {
        $query->where(function($q) use ($request) {
            $q->where('type_visibilite', $request->visibilite)
              ->orWhere('type_visibilite', 'tous');
        });
    }

    if ($request->filled('statut')) {
        match($request->statut) {
            'actif'   => $query->actif()->where('fin_partenariat', '>', now()),
            'expire'  => $query->where('fin_partenariat', '<', now()),
            'bientot' => $query->expirentBientot(30),
            default   => null,
        };
    }

    if ($request->filled('search')) {
        $query->where('nom', 'like', '%'.$request->search.'%');
    }

    $sponsors = $query->paginate(15);

    $stats = [
        'total'     => $club->sponsors()->count(),
        'actifs'    => $club->sponsors()->actif()->count(),
        'expirient' => $club->sponsors()->expirentBientot(30)->count(),
    ];

    return view('club.sponsors.index', compact('club', 'sponsors', 'stats'));
}

    // ═══ Afficher sponsor ═══
    public function show(Sponsor $sponsor)
    {
        $this->authorize('view', $sponsor);
        return view('club.sponsors.show', compact('sponsor'));
    }

    // ═══ Formulaire création ═══
    public function create()
    {
        return view('club.sponsors.create');
    }

    // ═══ Enregistrer sponsor ═══
    public function store(Request $request)
        {
            $club = Auth::user()->club;

            $validated = $request->validate([
                'nom'                  => 'required|string|max:200',
                'email_contact'        => 'required|email',
                'telephone_contact'    => 'required|string|max:20',
                'site_web'             => 'nullable|url',
                'description'          => 'nullable|string',
                'type_visibilite'      => 'required|in:logo,banniere,tous',
                'montant_contrat'      => 'nullable|numeric|min:0',
                'devise'               => 'required|string|max:3',
                'montant_confidentiel' => 'sometimes|boolean',
                'debut_partenariat'    => 'required|date',
                'fin_partenariat'      => 'required|date|after:debut_partenariat',
                'logo'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);

            if ($request->hasFile('logo')) {
                $validated['logo'] = $request->file('logo')->store('sponsors', 'public');
            }

            $validated['club_id'] = $club->id;
            $validated['actif']   = true;

            $sponsor = Sponsor::create($validated);

            return response()->json(['message' => 'Sponsor ajouté.', 'id' => $sponsor->id], 201);
        }



    // ═══ Formulaire édition ═══
    public function edit(Sponsor $sponsor)
    {
        $this->authorize('update', $sponsor);
        return view('club.sponsors.edit', compact('sponsor'));
    }

    // ═══ Mettre à jour sponsor ═══
    public function update(Request $request, Sponsor $sponsor)
{
    $this->authorize('update', $sponsor);

    $validated = $request->validate([
        'nom'                  => 'required|string|max:200',
        'email_contact'        => 'required|email',
        'telephone_contact'    => 'required|string|max:20',
        'site_web'             => 'nullable|url',
        'description'          => 'nullable|string',
        'type_visibilite'      => 'required|in:logo,banniere,tous',
        'montant_contrat'      => 'nullable|numeric|min:0',
        'devise'               => 'required|string|max:3',
        'montant_confidentiel' => 'sometimes|boolean',
        'debut_partenariat'    => 'required|date',
        'fin_partenariat'      => 'required|date|after:debut_partenariat',
        'actif'                => 'sometimes|boolean',
        'logo'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
    ]);

    if ($request->hasFile('logo')) {
        if ($sponsor->logo) {
         Storage::disk('public')->delete($sponsor->logo);
            }
        $validated['logo'] = $request->file('logo')->store('sponsors', 'public');
    }

    $validated['montant_confidentiel'] = $request->boolean('montant_confidentiel');
    $validated['actif']                = $request->boolean('actif');

    $sponsor->update($validated);

    return response()->json(['message' => 'Sponsor mis à jour.']);
}

    // ═══ Supprimer sponsor ═══
    public function destroy(Sponsor $sponsor)
        {
            $this->authorize('delete', $sponsor);
            $sponsor->delete();
            return response()->json(['message' => 'Sponsor supprimé.']);
        }
}
