<?php
// app/Http/Controllers/Club/LicenceController.php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Joueur;
use App\Models\Licence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LicenceController extends Controller
{
    // ═══ Liste des licences du club ═══
            public function index(Request $request)
        {
            $club  = Auth::user()->club;
            $query = $club->licences()->with('joueur.user')->orderBy('date_expiration', 'asc');

            if ($request->filled('categorie')) {
                $query->where('categorie', $request->categorie);
            }

            if ($request->filled('search')) {
                $query->whereHas('joueur.user', fn($q) =>
                    $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('prenom', 'like', '%'.$request->search.'%')
                );
            }

            if ($request->filled('statut')) {
                match($request->statut) {
                    'active'  => $query->active()->where('date_expiration', '>', now()),
                    'bientot' => $query->expirentBientot(30),
                    'expiree' => $query->expirees(),
                    default   => null,
                };
            }

            $licences        = $query->paginate(20);
            $expirentBientot = $club->licences()->active()->expirentBientot(30)->with('joueur.user')->get();

            return view('club.licences.index', compact('club', 'licences', 'expirentBientot'));
        }
    // ═══ Formulaire création ═══
    public function create()
    {
        $club    = Auth::user()->club;
        $joueurs = $club->joueurs()->with('user')->get();
        return view('club.licences.create', compact('club', 'joueurs'));
    }

    // ═══ Enregistrer licence ═══
    public function store(Request $request)
{
    $request->validate([
        'joueur_id'       => 'required|exists:joueurs,id',
        'numero_licence'  => 'nullable|string|max:80|unique:licences,numero_licence',
        'categorie'       => 'required|in:junior,cadet,senior,veteran',
        'date_debut'      => 'required|date',
        'date_expiration' => 'required|date|after:date_debut',
        'fichier_pdf'     => 'required|file|mimes:pdf|max:5120',
        'note'            => 'nullable|string',
    ]);

    $club    = Auth::user()->club;
    $pdfPath = $request->file('fichier_pdf')
                       ->store('licences/' . $club->id, 'public');

    Licence::where('joueur_id', $request->joueur_id)
           ->where('active', 1)
           ->update(['active' => 0]);

    $licence = Licence::create([
        'joueur_id'       => $request->joueur_id,
        'club_id'         => $club->id,
        'numero_licence'  => $request->numero_licence,
        'fichier_pdf'     => $pdfPath,
        'categorie'       => $request->categorie,
        'date_debut'      => $request->date_debut,
        'date_expiration' => $request->date_expiration,
        'note'            => $request->note,
        'active'          => 1,
    ]);

    return response()->json([
        'message' => 'Licence ajoutée avec succès.',
        'licence' => $licence->id,
    ], 201);
}

    // ═══ Afficher licence ═══
    public function show(Licence $licence)
    {
        $this->autoriserAcces($licence);
        $licence->load('joueur.user');
        return view('club.licences.show', compact('licence'));
    }

    // ═══ Télécharger PDF ═══
    public function download(Licence $licence)
    {
        return response()->download(storage_path('app/public/' . $licence->fichier_pdf));
    }

    // ═══ Renouveler licence ═══
            public function renouveler(Request $request, Licence $licence)
        {
            $this->autoriserAcces($licence);

            $request->validate([
                'date_debut'      => 'required|date',
                'date_expiration' => 'required|date|after:date_debut',
                'fichier_pdf'     => 'required|file|mimes:pdf|max:5120',
            ]);

            $licence->update(['active' => 0]);

            $club    = Auth::user()->club;
            $pdfPath = $request->file('fichier_pdf')
                            ->store('licences/' . $club->id, 'public');

            $nouvelle = Licence::create([
                'joueur_id'       => $licence->joueur_id,
                'club_id'         => $club->id,
                'numero_licence'  => $licence->numero_licence,
                'fichier_pdf'     => $pdfPath,
                'categorie'       => $licence->categorie,
                'date_debut'      => $request->date_debut,
                'date_expiration' => $request->date_expiration,
                'active'          => 1,
            ]);

            $expiree       = $nouvelle->estExpiree();
            $joursRestants = $nouvelle->joursRestants();

            return response()->json([
                'date_debut'      => $nouvelle->date_debut->format('d/m/Y'),
                'date_expiration' => $nouvelle->date_expiration->format('d/m/Y'),
                'statut_label'    => $expiree ? 'Expirée' : ($joursRestants <= 30 ? 'Expire bientôt' : 'Active'),
                'statut_class'    => $expiree ? 'bg-danger' : ($joursRestants <= 30 ? 'bg-warning text-dark' : 'bg-success'),
            ]);
        }

    // ═══ Supprimer licence ═══
    public function destroy(Licence $licence)
        {
            $this->autoriserAcces($licence);
            Storage::disk('public')->delete($licence->fichier_pdf);
            $licence->delete();

            return response()->json(['message' => 'Licence supprimée.']);
        }

    // ═══ Vérifier accès ═══
    private function autoriserAcces(Licence $licence): void
    {
        if ($licence->club_id !== Auth::user()->club->id) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function exportPdf()
{
    $club = Auth::user()->club;
    $licences = $club->licences()->with('joueur.user')->get();

    $pdf = PDF::loadView('pdf.licences', compact('club', 'licences'));
    return $pdf->download('licences_' . $club->id . '.pdf');
}
}