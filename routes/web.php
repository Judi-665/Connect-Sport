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

use App\Http\Controllers\Joueur\CarriereController;
use App\Http\Controllers\Joueur\DifficulteController;
use App\Http\Controllers\Joueur\JoueurController;
use App\Http\Controllers\Joueur\StatistiqueController;
use App\Http\Controllers\Joueur\AbonnementController as JoueurAbonnementController;

use App\Http\Controllers\Messagerie\MessageController;

use App\Http\Controllers\Opportunite\OpportuniteController;

use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Supporter\SupporterController;
use App\Http\Controllers\Transfert\AgentController;
use App\Http\Controllers\Transfert\TransfertController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PAGE D'ACCUEIL PUBLIQUE
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Clubs publics
Route::get('/clubs', [ClubController::class, 'index'])->name('clubs.index');
Route::get('/clubs/{club:slug}', [ClubController::class, 'show'])->name('clubs.show');

// Joueurs sans club (vitrine recruteurs)
Route::get('/joueurs/disponibles', [JoueurController::class, 'sansClub'])->name('joueurs.sans-club');

// Opportunités publiques
Route::get('/opportunites', [OpportuniteController::class, 'index'])->name('opportunites.index');
Route::get('/opportunites/{opportunite}', [OpportuniteController::class, 'show'])->name('opportunites.show');

Route::get('/joueurs', [JoueurController::class, 'index'])->name('joueurs.index');
Route::get('/joueurs/{joueur}', [JoueurController::class, 'show'])->name('joueurs.show');

/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
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
    Route::get('/profil',  [ClubController::class, 'edit'])->name('profil.edit');
    Route::put('/profil',  [ClubController::class, 'update'])->name('profil.update');

    // ── Paramètres ──
    Route::prefix('parametres')->name('parametres.')->group(function () {
        Route::get('/',             [ClubController::class,   'parametres'])->name('index');
        Route::put('/profil',       [ClubController::class,   'update'])->name('profil');
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

    // ── Fonctionnalités de base (plan gratuit et +) ──
    Route::prefix('joueurs')->name('joueurs.')->group(function () {
        Route::get('/',                    [ClubController::class, 'joueurs'])->name('index');
        Route::get('/{joueur}',            [ClubController::class, 'showJoueur'])->name('show');
        Route::post('/{joueur}/stats',     [StatistiqueController::class, 'store'])->name('stats.store');
        Route::put('/stats/{statistique}', [StatistiqueController::class, 'update'])->name('stats.update');
    });

    Route::prefix('equipes')->name('equipes.')->group(function () {
        Route::get('/',                    [EquipeController::class, 'index'])->name('index');
        Route::get('/creer',               [EquipeController::class, 'create'])->name('create');
        Route::post('/',                   [EquipeController::class, 'store'])->name('store');
        Route::get('/{equipe}/modifier',   [EquipeController::class, 'edit'])->name('edit');
        Route::put('/{equipe}',            [EquipeController::class, 'update'])->name('update');
        Route::delete('/{equipe}',         [EquipeController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/',                     [AgendaController::class, 'index'])->name('index');
        Route::get('/creer',                [AgendaController::class, 'create'])->name('create');
        Route::post('/',                    [AgendaController::class, 'store'])->name('store');
        Route::get('/{evenement}',          [AgendaController::class, 'show'])->name('show');
        Route::get('/{evenement}/modifier', [AgendaController::class, 'edit'])->name('edit');
        Route::put('/{evenement}',          [AgendaController::class, 'update'])->name('update');
        Route::delete('/{evenement}',       [AgendaController::class, 'destroy'])->name('destroy');
    });

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

    Route::middleware('abonnement:stats_avancees')->group(function () {
        Route::get('/statistiques', [ClubController::class, 'statistiques'])->name('statistiques');
    });
});

// Callback FedaPay 
Route::match(['get', 'post'], '/club/abonnement/callback', [AbonnementController::class, 'callback'])
    ->name('club.abonnement.callback');


/*
|--------------------------------------------------------------------------
| JOUEUR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:joueur'])->prefix('joueur')->name('joueur.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [JoueurController::class, 'dashboard'])->name('dashboard');
    Route::get('/locked', [JoueurController::class, 'locked'])->name('locked');

    // Profil
    Route::get('/profil/creer', [JoueurController::class, 'create'])->name('profil.create');
    Route::post('/profil', [JoueurController::class, 'store'])->name('profil.store');
    Route::get('/profil', [JoueurController::class, 'edit'])->name('profil');
    Route::get('/profile', [JoueurController::class, 'edit'])->name('profile'); // alias compat
    Route::put('/profil', [JoueurController::class, 'update'])->name('profil.update');

    // ── Abonnement ──
    Route::prefix('abonnement')->name('abonnement.')->group(function () {
        Route::get('/',           [JoueurAbonnementController::class, 'index'])->name('index');
        Route::get('/choisir',    [JoueurAbonnementController::class, 'choisir'])->name('choisir');
        Route::post('/souscrire', [JoueurAbonnementController::class, 'souscrire'])->name('souscrire');
        Route::get('/succes',     [JoueurAbonnementController::class, 'succes'])->name('succes');
    });

    // Profil public
    Route::get('/{joueur}', [JoueurController::class, 'show'])->name('show');

    // Carrière & IA
    Route::get('/carriere', [CarriereController::class, 'index'])->name('carriere.index');
    Route::get('/carriere/parcours', [CarriereController::class, 'parcours'])->name('carriere.parcours');
    Route::post('/carriere/objectif', [CarriereController::class, 'setObjectif'])->name('carriere.objectif');
    Route::get('/carriere/recommandations', [CarriereController::class, 'recommandations'])->name('carriere.recommandations');

    // Difficultés & Souhaits
    Route::prefix('difficultes')->name('difficultes.')->group(function () {
        Route::get('/', [DifficulteController::class, 'index'])->name('index');
        Route::post('/', [DifficulteController::class, 'store'])->name('store');
        Route::patch('/{difficulte}/resolu', [DifficulteController::class, 'marquerResolu'])->name('resolu');
        Route::delete('/{difficulte}', [DifficulteController::class, 'destroy'])->name('destroy');
    });

    // Transferts (côté joueur)
    Route::prefix('transferts')->name('transferts.')->group(function () {
        Route::get('/', [TransfertController::class, 'indexJoueur'])->name('index');
        Route::get('/demande', [TransfertController::class, 'create'])->name('create');
        Route::post('/demande', [TransfertController::class, 'store'])->name('store');
        Route::get('/{transfert}', [TransfertController::class, 'showJoueur'])->name('show');
    });

    // Opportunités (côté joueur)
    Route::get('/opportunites', [OpportuniteController::class, 'indexJoueur'])->name('opportunites.index');

});

//  Callback FedaPay 
Route::match(['get', 'post'], '/joueur/abonnement/callback', [JoueurAbonnementController::class, 'callback'])
    ->name('joueur.abonnement.callback');

/*
|--------------------------------------------------------------------------
| PARENT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/joueur/{joueur}', [ParentController::class, 'showJoueur'])->name('joueur.show');
    Route::get('/messages', [ParentController::class, 'messages'])->name('messages');
});

/*
|--------------------------------------------------------------------------
| AGENT
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentController::class, 'dashboard'])->name('dashboard');
    Route::get('/profil/creer', [AgentController::class, 'create'])->name('create');
    Route::post('/profil', [AgentController::class, 'store'])->name('store');
    Route::get('/joueurs', [AgentController::class, 'mesJoueurs'])->name('joueurs');
    Route::get('/transferts', [AgentController::class, 'mesTransferts'])->name('transferts');
    Route::get('/transferts/creer', [AgentController::class, 'createOffre'])->name('transferts.create');
    Route::post('/transferts', [AgentController::class, 'storeOffre'])->name('transferts.store');
    Route::get('/transferts/historique', [AgentController::class, 'historique'])->name('transferts.historique');
    Route::get('/transferts/{transfert}', [AgentController::class, 'showTransfert'])->name('transferts.show');
    Route::post('/transferts/{transfert}/negocier', [AgentController::class, 'negocier'])->name('negocier');
    Route::get('/clubs-partenaires', [AgentController::class, 'partenaires'])->name('partenaires');
    Route::get('/profil', [AgentController::class, 'edit'])->name('profil');
    Route::put('/profil', [AgentController::class, 'update'])->name('profil.update');
});

/*
|--------------------------------------------------------------------------
| SUPPORTER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:supporter'])->prefix('supporter')->name('supporter.')->group(function () {
    Route::get('/dashboard', [SupporterController::class, 'dashboard'])->name('dashboard');
    Route::post('/clubs/{club}/suivre', [SupporterController::class, 'suivreClub'])->name('club.suivre');
    Route::delete('/clubs/{club}/quitter', [SupporterController::class, 'quitterClub'])->name('club.quitter');
    Route::get('/clubs', [SupporterController::class, 'mesClubs'])->name('clubs');
});

/*
|--------------------------------------------------------------------------
| MESSAGERIE (tous rôles authentifiés)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [MessageController::class, 'inbox'])->name('inbox');
    Route::get('/non-lus', [MessageController::class, 'nonLus'])->name('non-lus');
    Route::get('/archives', [MessageController::class, 'archives'])->name('archives');
    Route::get('/recherche', [MessageController::class, 'search'])->name('search');
    Route::post('/envoyer', [MessageController::class, 'send'])->name('send');
    Route::get('/{user}/conversation', [MessageController::class, 'conversation'])->name('conversation');
    Route::patch('/{message}/lire', [MessageController::class, 'markAsRead'])->name('read');
    Route::patch('/lire-tout', [MessageController::class, 'markAllAsRead'])->name('read-all');
    Route::patch('/{message}/archiver', [MessageController::class, 'archive'])->name('archive');
    Route::delete('/{message}', [MessageController::class, 'destroy'])->name('destroy');
});


Route::middleware('auth')->post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
     ->name('profile.avatar');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/clubs', [AdminController::class, 'clubs'])->name('clubs');
    Route::get('/joueurs', [AdminController::class, 'joueurs'])->name('joueurs');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/transferts', [AdminController::class, 'transferts'])->name('transferts');
    Route::get('/paiements', [AdminController::class, 'paiements'])->name('paiements');
});