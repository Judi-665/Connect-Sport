<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ParentJoueur;
use App\Models\Message;
use App\Models\User;
use App\Policies\MessagePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));
            $ip = (string) $request->ip();

            return [
                Limit::perMinute(5)->by('login:' . $email . '|' . $ip),
                Limit::perMinute(30)->by('login-ip:' . $ip),
            ];
        });

        // Enregistrement des policies
        Gate::policy(Message::class, MessagePolicy::class);

        // ─── View Composer : injecte $liens dans la sidebar parent ───
        View::composer(['parent.partials._sidebar', 'layouts.parent'], function ($view) {
            $user = Auth::user();
            if ($user instanceof User && $user->isParent()) {
                $liens = $user->parentsJoueurs()
                    ->actif()
                    ->whereNotNull('joueur_id')
                    ->has('joueur')
                    ->with(['joueur.user', 'joueur.club'])
                    ->get();

                $view->with('liens', $liens);
            }
        });
    }
}
