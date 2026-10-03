<?php

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\ClubPublicController;
use App\Http\Controllers\OpportunitePublicController;
use App\Http\Controllers\JoueurPublicController;

use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\Club\AgendaController;
use App\Http\Controllers\Club\ClubController;
use App\Http\Controllers\Club\EquipeController;
use App\Http\Controllers\Club\LicenceController;
use App\Http\Controllers\Club\MediaController;
use App\Http\Controllers\Club\SponsorController;
use App\Http\Controllers\Club\ProfileController;
use App\Http\Controllers\Club\AbonnementController;
use App\Http\Controllers\Club\StatistiqueController;
use App\Http\Controllers\Club\CandidatureController as ClubCandidatureController;

use App\Http\Controllers\Joueur\CarriereController;
use App\Http\Controllers\Joueur\CandidatureController as JoueurCandidatureController;
use App\Http\Controllers\Joueur\DifficulteController;
use App\Http\Controllers\Joueur\JoueurController;
use App\Http\Controllers\Joueur\AgendaController as JoueurAgendaController;

use App\Http\Controllers\Joueur\AbonnementController as JoueurAbonnementController;

use App\Http\Controllers\Messagerie\MessageController;

use App\Http\Controllers\Opportunite\OpportuniteController;

use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Supporter\SupporterController;
use App\Http\Controllers\Transfert\AgentController;
use App\Http\Controllers\Agent\AbonnementController as AgentAbonnementController;
use App\Http\Controllers\Transfert\TransfertController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PAGE D'ACCUEIL PUBLIQUE
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club:slug}/joueurs', [ClubController::class, 'publicJoueurs'])->name('clubs.joueurs');
Route::get('/clubs/{club:slug}/medias', [ClubController::class, 'publicMedias'])->name('clubs.medias');
Route::get('/clubs/{club:slug}/agenda', [ClubPublicController::class, 'agenda'])->name('clubs.agenda');
Route::get('/clubs/{club:slug}', [ClubController::class, 'show'])->name('clubs.show');

Route::get('/joueurs/disponibles', [JoueurController::class, 'sansClub'])->name('joueurs.sans-club');

Route::middleware('not-supporter')->group(function () {
    Route::get('/opportunites', [OpportuniteController::class, 'index'])->name('opportunites.index');
    Route::get('/opportunites/{opportunite}', [OpportuniteController::class, 'show'])->name('opportunites.show');
});

Route::get('/joueurs', [JoueurController::class, 'index'])->name('joueurs.index');
Route::get('/joueurs/{joueur}', [JoueurController::class, 'show'])->name('joueurs.show');

Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show');

Route::get('/medias/{media}/fichier', [\App\Http\Controllers\Club\MediaController::class, 'file'])
    ->name('medias.file');
Route::post('/medias/{media}/reactions', [\App\Http\Controllers\MediaReactionController::class, 'toggle'])
    ->middleware('auth')
    ->name('medias.reactions.toggle');

/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);
});

Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| CLUB
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:club'])->prefix('club')->name('club.')->group(function () {

    // ── Dashboard ──
    Route::get('/dashboard', [ClubController::class, 'dashboard'])->name('dashboard');
    Route::get('/locked', function () {
        return view('club.locked', [
            'planRequis' => request('planRequis', 'standard'),
            'message'    => request('message'),
        ]);
    })->name('locked');

    // ── Profil ──
    Route::get('/profil', [ClubController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ClubController::class, 'update'])->name('profil.update');

    // ── Paramètres ──
    Route::prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/',             [ClubController::class, 'parametres'])->name('index');
        Route::put('/profil',       [ClubController::class, 'update'])->name('profil');
        Route::put('/compte',       [ProfileController::class, 'update'])->name('compte');
        Route::put('/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/compte',    [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ── Abonnement ──
    Route::prefix('abonnement')->name('abonnement.')->group(function () {
        Route::get('/',           [AbonnementController::class, 'index'])->name('index');
        Route::get('/choisir',    [AbonnementController::class, 'choisir'])->name('choisir');
        Route::post('/souscrire', [AbonnementController::class, 'souscrire'])->name('souscrire');
        Route::get('/succes',     [AbonnementController::class, 'succes'])->name('succes');
    });

    // ── Joueurs (gratuit) ──
    Route::prefix('joueurs')->name('joueurs.')->group(function () {
        Route::get('/',                    [ClubController::class, 'joueurs'])->name('index');
        Route::get('/{joueur}',            [ClubController::class, 'showJoueur'])->name('show');
        Route::post('/{joueur}/stats',     [StatistiqueController::class, 'storeDepuisJoueur'])->name('stats.store');
        Route::put('/stats/{statistique}', [StatistiqueController::class, 'update'])->name('stats.update');
    });

    // ── Candidatures reçues (gratuit) ──
    Route::prefix('candidatures')->name('candidatures.')->group(function () {
        Route::get('/',                        [ClubCandidatureController::class, 'index'])->name('index');
        Route::get('/{candidature}',           [ClubCandidatureController::class, 'show'])->name('show');
        Route::post('/{candidature}/accepter', [ClubCandidatureController::class, 'accepter'])->name('accepter');
        Route::post('/{candidature}/refuser',  [ClubCandidatureController::class, 'refuser'])->name('refuser');
    });

    // ── Équipes (gratuit) ──
    Route::prefix('equipes')->name('equipes.')->group(function () {
        Route::get('/',                  [EquipeController::class, 'index'])->name('index');
        Route::get('/creer',             [EquipeController::class, 'create'])->name('create');
        Route::post('/',                 [EquipeController::class, 'store'])->name('store');
        Route::get('/{equipe}/modifier', [EquipeController::class, 'edit'])->name('edit');
        Route::put('/{equipe}',          [EquipeController::class, 'update'])->name('update');
        Route::delete('/{equipe}',       [EquipeController::class, 'destroy'])->name('destroy');
    });

    // ── Agenda (gratuit) + saisie stats liée à un événement ──
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/',                     [AgendaController::class, 'index'])->name('index');
        Route::get('/creer',                [AgendaController::class, 'create'])->name('create');
        Route::post('/',                    [AgendaController::class, 'store'])->name('store');
        Route::get('/{evenement}',          [AgendaController::class, 'show'])->name('show');
        Route::get('/{evenement}/modifier', [AgendaController::class, 'edit'])->name('edit');
        Route::put('/{evenement}',          [AgendaController::class, 'update'])->name('update');
        Route::delete('/{evenement}',       [AgendaController::class, 'destroy'])->name('destroy');
        Route::get('/{evenement}/stats',    [StatistiqueController::class, 'create'])->name('stats.create');
        Route::post('/{evenement}/stats',   [StatistiqueController::class, 'store'])->name('stats.store');
    });

    // ── Validation d'une statistique ──
    Route::patch('/statistiques/{statistique}/valider', [StatistiqueController::class, 'valider'])->name('statistiques.valider');

    // ── Licences (gratuit) ──
    Route::prefix('licences')->name('licences.')->group(function () {
        Route::get('/',                      [LicenceController::class, 'index'])->name('index');
        Route::get('/ajouter',               [LicenceController::class, 'create'])->name('create');
        Route::get('/export/pdf',            [LicenceController::class, 'exportPdf'])->name('export');
        Route::post('/',                     [LicenceController::class, 'store'])->name('store');
        Route::get('/{licence}',             [LicenceController::class, 'show'])->name('show');
        Route::delete('/{licence}',          [LicenceController::class, 'destroy'])->name('destroy');
        Route::get('/{licence}/telecharger', [LicenceController::class, 'download'])->name('download');
        Route::post('/{licence}/renouveler', [LicenceController::class, 'renouveler'])->name('renouveler');
    });

    // ── Transferts (standard) 🔒 ──
    // ⚠️ approuver()/rejeter() non implémentées dans TransfertController (seul updateStatut() existe) — à corriger si utilisées en vue
    Route::prefix('transferts')->name('transferts.')->group(function () {
        Route::get('/',                       [TransfertController::class, 'index'])->name('index');
        Route::get('/creer',                  [TransfertController::class, 'create'])->name('create');
        Route::post('/',                      [TransfertController::class, 'store'])->name('store');
        Route::get('/{transfert}',            [TransfertController::class, 'show'])->name('show');
        Route::get('/{transfert}/modifier',   [TransfertController::class, 'edit'])->name('edit');
        Route::put('/{transfert}',            [TransfertController::class, 'update'])->name('update');
        Route::delete('/{transfert}',         [TransfertController::class, 'destroy'])->name('destroy');
        Route::post('/{transfert}/approuver', [TransfertController::class, 'approuver'])->name('approuver');
        Route::post('/{transfert}/rejeter',   [TransfertController::class, 'rejeter'])->name('rejeter');
        Route::patch('/{transfert}/statut',   [TransfertController::class, 'updateStatut'])->name('statut');
    });

    // ── Statistiques joueurs (standard)  ──
    Route::middleware('abonnement:standard')->prefix('statistiques')->name('statistiques.')->group(function () {
        Route::get('/',         [StatistiqueController::class, 'indexClub'])->name('index');
        Route::get('/enregistrer', [StatistiqueController::class, 'enregistrer'])->name('enregistrer');
        Route::post('/enregistrer', [StatistiqueController::class, 'enregistrerStore'])->name('enregistrer.store');
        Route::get('/avancees', [ClubController::class, 'statistiques'])->name('avancees')
            ->middleware('abonnement:stats_avancees');
        Route::get('/{joueur}', [StatistiqueController::class, 'index'])->name('joueur');
        Route::patch('/valider/{statistique}', [StatistiqueController::class, 'valider'])->name('valider');
    });

    // ── Opportunités (standard) 🔒 ──
    Route::prefix('opportunites')->name('opportunites.')->group(function () {
        Route::get('/',                       [OpportuniteController::class, 'indexClub'])->name('index');
        Route::get('/creer',                  [OpportuniteController::class, 'create'])->name('create');
        Route::post('/',                      [OpportuniteController::class, 'store'])->name('store');
        Route::get('/{opportunite}',          [OpportuniteController::class, 'show'])->name('show');
        Route::get('/{opportunite}/modifier', [OpportuniteController::class, 'edit'])->name('edit');
        Route::put('/{opportunite}',          [OpportuniteController::class, 'update'])->name('update');
        Route::delete('/{opportunite}',       [OpportuniteController::class, 'destroy'])->name('destroy');
    });

    // ── Fonctionnalités Premium ──
    Route::middleware('abonnement:galerie_media')->group(function () {
        Route::prefix('medias')->name('medias.')->group(function () {
            Route::get('/',                     [MediaController::class, 'index'])->name('index');
            Route::get('/upload',               [MediaController::class, 'create'])->name('create');
            Route::post('/upload',              [MediaController::class, 'store'])->name('store');
            Route::delete('/{media}',           [MediaController::class, 'destroy'])->name('destroy');
            Route::patch('/{media}/visibilite', [MediaController::class, 'updateVisibilite'])->name('visibilite');
        });
    });

    Route::middleware('abonnement:sponsors')->group(function () {
        Route::prefix('sponsors')->name('sponsors.')->group(function () {
            Route::get('/',                   [SponsorController::class, 'index'])->name('index');
            Route::get('/ajouter',            [SponsorController::class, 'create'])->name('create');
            Route::post('/',                  [SponsorController::class, 'store'])->name('store');
            Route::get('/{sponsor}',          [SponsorController::class, 'show'])->name('show');
            Route::get('/{sponsor}/modifier', [SponsorController::class, 'edit'])->name('edit');
            Route::put('/{sponsor}',          [SponsorController::class, 'update'])->name('update');
            Route::delete('/{sponsor}',       [SponsorController::class, 'destroy'])->name('destroy');
        });
    });

});

Route::match(['get', 'post'], '/club/abonnement/callback', [AbonnementController::class, 'callback'])
    ->name('club.abonnement.callback');

/*
|--------------------------------------------------------------------------
| JOUEUR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:joueur'])->prefix('joueur')->name('joueur.')->group(function () {

    // ── Dashboard ──
    Route::get('/dashboard', [JoueurController::class, 'dashboard'])->name('dashboard');
    Route::get('/locked', [JoueurController::class, 'locked'])->name('locked');
    Route::get('/demandes-parents', [JoueurController::class, 'demandesParents'])->name('parents.demandes');
    Route::patch('/demandes-parents/{parent}/accepter', [JoueurController::class, 'accepterDemandeParent'])->name('parents.accepter');
    Route::delete('/demandes-parents/{parent}', [JoueurController::class, 'refuserDemandeParent'])->name('parents.refuser');

    // ── Profil ──
    Route::get('/profil/creer', [JoueurController::class, 'create'])->name('profil.create');
    Route::post('/profil',      [JoueurController::class, 'store'])->name('profil.store');
    Route::get('/profil',       [JoueurController::class, 'edit'])->name('profil');
    Route::get('/profile',      [JoueurController::class, 'edit'])->name('profile'); // alias compat
    Route::put('/profil',       [JoueurController::class, 'update'])->name('profil.update');

    // ── Abonnement ──
    Route::prefix('abonnement')->name('abonnement.')->group(function () {
        Route::get('/',           [JoueurAbonnementController::class, 'index'])->name('index');
        Route::get('/choisir',    [JoueurAbonnementController::class, 'choisir'])->name('choisir');
        Route::post('/souscrire', [JoueurAbonnementController::class, 'souscrire'])->name('souscrire');
        Route::get('/succes',     [JoueurAbonnementController::class, 'succes'])->name('succes');
    });

    // ── Carrière (standard) 🔒 — module IA (parcours/objectif/recommandations) volontairement absent pour l'instant ──
    Route::prefix('carriere')->name('carriere.')->group(function () {
        Route::get('/',             [CarriereController::class, 'index'])->name('index');
        Route::get('/profil',       [CarriereController::class, 'profil'])->name('profil');
        Route::get('/statistiques', [CarriereController::class, 'statistiquesGlobales'])->name('statistiques');
        Route::get('/cv',           [CarriereController::class, 'downloadCV'])->name('cv');
    });

    // ── Candidatures — postuler à un club (gratuit) ──
    Route::prefix('candidatures')->name('candidatures.')->group(function () {
        Route::get('/',                 [JoueurCandidatureController::class, 'indexJoueur'])->name('index');
        Route::get('/postuler/{club}',  [JoueurCandidatureController::class, 'create'])->name('create');
        Route::post('/postuler/{club}', [JoueurCandidatureController::class, 'store'])->name('store');
        Route::delete('/{candidature}', [JoueurCandidatureController::class, 'annuler'])->name('annuler');
    });

    Route::get('/agenda', [JoueurAgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/{evenement}', [JoueurAgendaController::class, 'show'])->name('agenda.show');
    

    // ── Transferts (standard) 
    Route::prefix('transferts')->name('transferts.')->group(function () {
        Route::get('/',             [TransfertController::class, 'indexJoueur'])->name('index');
        Route::get('/demande',      [TransfertController::class, 'createJoueur'])->name('create');
        Route::post('/demande',     [TransfertController::class, 'storeJoueur'])->name('store');
        Route::get('/{transfert}',  [TransfertController::class, 'showJoueur'])->name('show');
    });

    // ── Opportunités (standard) 
    Route::get('/opportunites', [OpportuniteController::class, 'indexJoueur'])->name('opportunites.index');
    Route::post('/opportunites/{opportunite}/candidater', [OpportuniteController::class, 'candidater'])->name('opportunites.candidater');

    // ── Profil public 
    Route::get('/{joueur}', [JoueurController::class, 'show'])->name('show');
});

Route::match(['get', 'post'], '/joueur/abonnement/callback', [JoueurAbonnementController::class, 'callback'])
    ->name('joueur.abonnement.callback');

/*
|--------------------------------------------------------------------------
| PARENT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    // ── Dashboard ──
    Route::get('/dashboard',                         [ParentController::class, 'dashboard'])->name('dashboard');

    // ── Lier un enfant ──
    Route::get('/ajouter-enfant',                    [ParentController::class, 'addJoueur'])->name('add-joueur');
    Route::post('/ajouter-enfant',                   [ParentController::class, 'confirmJoueur'])->middleware('throttle:5,1')->name('confirm-joueur');

    // ── Profil enfant ──
    Route::get('/joueur/{joueur}',                   [ParentController::class, 'showJoueur'])->name('joueur.show');

    // ── Statistiques enfant ──
    Route::get('/joueur/{joueur}/statistiques',      [ParentController::class, 'statJoueur'])->name('joueur.stats');

    // ── Agenda enfant ──
    Route::get('/joueur/{joueur}/agenda',            [ParentController::class, 'agendaJoueur'])->name('joueur.agenda');

    // ── Contacter le club ──
    Route::get('/joueur/{joueur}/contacter-club',    [ParentController::class, 'contacterClub'])->name('joueur.contacter-club');

    // ── Signaler difficulté / voeu ──
    Route::get('/joueur/{joueur}/signalement',       [ParentController::class, 'signalement'])->name('joueur.signalement');
    Route::post('/joueur/{joueur}/signalement',      [ParentController::class, 'storeSignalement'])->name('joueur.signalement.store');

    // ── Transferts de l'enfant (lecture seule) ──
    Route::get('/joueur/{joueur}/transferts',        [ParentController::class, 'transfertsJoueur'])->name('joueur.transferts');

    // ── Retirer un enfant ──
    Route::delete('/liens/{parent}',                 [ParentController::class, 'removeJoueur'])->name('lien.remove');

    // ── Profil parent ──
    Route::get('/profil',                            [ParentController::class, 'profil'])->name('profil');
    Route::put('/profil',                            [ParentController::class, 'updateProfil'])->name('profil.update');
    Route::put('/mot-de-passe',                      [ParentController::class, 'updatePassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| AGENT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard',    [AgentController::class, 'dashboard'])->name('dashboard');
    Route::get('/locked', function () {
        return view('agent.locked', ['planRequis' => request('planRequis', 'standard')]);
    })->name('locked');
    Route::get('/profil/creer', [AgentController::class, 'create'])->name('create');
    Route::post('/profil',      [AgentController::class, 'store'])->name('store');
    Route::get('/joueurs',      [AgentController::class, 'mesJoueurs'])->name('joueurs');

    Route::get('/joueurs/{joueur}/mandat/creer',      [AgentController::class, 'createMandat'])->name('joueurs.mandat.create');
    Route::post('/joueurs/{joueur}/mandat',           [AgentController::class, 'representeJoueur'])->name('joueurs.mandat.store');
    Route::patch('/joueurs/{joueur}/mandat/resilier', [AgentController::class, 'resilierMandat'])->name('joueurs.mandat.resilier');
    Route::get('/commissions', [AgentController::class, 'commissions'])->name('commissions');
    Route::get('/statistiques', [AgentController::class, 'statistiques'])->name('statistiques');

    Route::prefix('transferts')->name('transferts.')->group(function () {
        Route::get('/',              [AgentController::class, 'mesTransferts'])->name('index');
        Route::get('/creer',         [AgentController::class, 'createOffre'])->name('create');
        Route::post('/',             [AgentController::class, 'storeOffre'])->name('store');
        Route::get('/historique',    [AgentController::class, 'historique'])->name('historique');
        Route::get('/{transfert}',   [AgentController::class, 'showTransfert'])->name('show');
        Route::post('/{transfert}/negocier', [AgentController::class, 'negocier'])->name('negocier');
    });

    Route::get('/clubs-partenaires', [AgentController::class, 'partenaires'])->name('partenaires');
    Route::get('/profil', [AgentController::class, 'edit'])->name('profil');
    Route::put('/profil', [AgentController::class, 'update'])->name('profil.update');

    Route::get('/opportunites',                       [AgentController::class, 'opportunites'])->name('opportunites.index');
    Route::post('/opportunites/{opportunite}/postuler', [AgentController::class, 'postulerOpportunite'])->name('opportunites.postuler');

    Route::prefix('abonnement')->name('abonnement.')->group(function () {
    Route::get('/',           [AgentAbonnementController::class, 'index'])->name('index');
    Route::get('/choisir',    [AgentAbonnementController::class, 'choisir'])->name('choisir');
    Route::post('/souscrire', [AgentAbonnementController::class, 'souscrire'])->name('souscrire');
    Route::get('/succes',     [AgentAbonnementController::class, 'succes'])->name('succes');
});


});

Route::match(['get', 'post'], '/agent/abonnement/callback', [AgentAbonnementController::class, 'callback'])
    ->name('agent.abonnement.callback');

/*
|--------------------------------------------------------------------------
| SUPPORTER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:supporter'])->prefix('supporter')->name('supporter.')->group(function () {
    Route::get('/dashboard',                            [SupporterController::class, 'dashboard'])->name('dashboard');
    Route::get('/clubs',                                [SupporterController::class, 'mesClubs'])->name('clubs');
    Route::get('/decouvrir',                            [SupporterController::class, 'decouvrir'])->name('decouvrir');
    Route::post('/clubs/{club}/suivre',                 [SupporterController::class, 'suivreClub'])->name('club.suivre');
    Route::delete('/clubs/{club}/quitter',              [SupporterController::class, 'quitterClub'])->name('club.quitter');
    Route::post('/clubs/{club}/notifications/toggle',   [SupporterController::class, 'toggleNotification'])->name('club.notifications.toggle');
    Route::get('/profil',                               [SupporterController::class, 'profil'])->name('profile');
    Route::get('/profil/edit',                          [SupporterController::class, 'profil'])->name('profil');
    Route::put('/profil',                               [SupporterController::class, 'updateProfil'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| MESSAGERIE (tous rôles authentifiés)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
    Route::get('/',                     [MessageController::class, 'inbox'])->name('inbox');
    Route::get('/non-lus',              [MessageController::class, 'nonLus'])->name('non-lus');
    Route::get('/archives',             [MessageController::class, 'archives'])->name('archives');
    Route::get('/recherche',            [MessageController::class, 'search'])->name('search');
    Route::post('/envoyer',             [MessageController::class, 'send'])->middleware('not-supporter')->name('send');
    Route::get('/{user}/conversation',  [MessageController::class, 'conversation'])->name('conversation');
    Route::patch('/{message}/lire',     [MessageController::class, 'markAsRead'])->name('read');
    Route::patch('/lire-tout',          [MessageController::class, 'markAllAsRead'])->name('read-all');
    Route::patch('/{message}/archiver', [MessageController::class, 'archive'])->name('archive');
    Route::delete('/{message}',         [MessageController::class, 'destroy'])->name('destroy');
});

Route::middleware('auth')->post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

use App\Http\Controllers\Admin\Auth\AdminAuthController;



/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION ADMIN (DÉDIÉE & SÉPARÉE)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/connexion', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/connexion', [AdminAuthController::class, 'login'])->middleware('throttle:login');
    });

    Route::post('/deconnexion', [AdminAuthController::class, 'logout'])->name('logout')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',              [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Utilisateurs
    Route::get('/users',                  [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}',           [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit',      [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}',           [AdminController::class, 'updateUser'])->name('users.update');
    Route::patch('/users/{user}/role',    [AdminController::class, 'updateRole'])->name('users.role');
    Route::patch('/users/{user}/block',   [AdminController::class, 'blockUser'])->name('users.block');
    Route::patch('/users/{user}/unblock', [AdminController::class, 'unblockUser'])->name('users.unblock');
    Route::delete('/users/{user}',        [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Clubs
    Route::get('/clubs',                  [AdminController::class, 'clubs'])->name('clubs');
    Route::get('/clubs/{club}',           [AdminController::class, 'showClub'])->name('clubs.show');
    Route::patch('/clubs/{club}/toggle',  [AdminController::class, 'toggleClub'])->name('clubs.toggle');

    // Joueurs
    Route::get('/joueurs',                      [AdminController::class, 'joueurs'])->name('joueurs');
    Route::get('/joueurs/{joueur}',             [AdminController::class, 'showJoueur'])->name('joueurs.show');
    Route::patch('/joueurs/{joueur}/visibilite',[AdminController::class, 'toggleVisibiliteJoueur'])->name('joueurs.visibilite');
    Route::patch('/joueurs/{joueur}/toggle',    [AdminController::class, 'toggleActifJoueur'])->name('joueurs.toggle');

    // Agents
    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/',                 [AdminController::class, 'agents'])->name('index');
        Route::get('/{agent}',          [AdminController::class, 'showAgent'])->name('show');
        Route::patch('/{agent}/verifier',[AdminController::class, 'verifierAgent'])->name('verifier');
        Route::patch('/{agent}/rejeter', [AdminController::class, 'rejeterAgent'])->name('rejeter');
        Route::patch('/{agent}/toggle',  [AdminController::class, 'toggleAgent'])->name('toggle');
    });

    // Transferts
    Route::get('/transferts',                       [AdminController::class, 'transferts'])->name('transferts');
    Route::get('/transferts/{transfert}',           [AdminController::class, 'showTransfert'])->name('transferts.show');
    Route::patch('/transferts/{transfert}/valider', [AdminController::class, 'validateTransfert'])->name('transferts.valider');
    Route::patch('/transferts/{transfert}/rejeter', [AdminController::class, 'rejectTransfert'])->name('transferts.rejeter');

    // Opportunités
    Route::get('/opportunites',                         [AdminController::class, 'opportunites'])->name('opportunites');
    Route::patch('/opportunites/{opportunite}/feature', [AdminController::class, 'featuredOpportunite'])->name('opportunites.feature');
    Route::patch('/opportunites/{opportunite}/toggle',  [AdminController::class, 'toggleOpportunite'])->name('opportunites.toggle');

    // Abonnements & Paiements
    Route::get('/paiements',              [AdminController::class, 'paiements'])->name('paiements');

    // Profil & Mot de passe Admin
    Route::get('/profil',                 [AdminController::class, 'profil'])->name('profile');
    Route::put('/profil',                 [AdminController::class, 'updateProfil'])->name('profile.update');
    Route::put('/mot-de-passe',           [AdminController::class, 'updatePassword'])->name('password.update');
});