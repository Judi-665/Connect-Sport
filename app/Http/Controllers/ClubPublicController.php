<?php
namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;

class ClubPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Club::with('sport');
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%'.$request->search.'%')
                  ->orWhere('ville', 'like', '%'.$request->search.'%');
        }
        if ($request->filled('sport')) {
            $query->whereHas('sport', fn($q) => $q->where('nom', $request->sport));
        }
        $clubs = $query->paginate(12);
        return view('clubs.index', compact('clubs'));
    }

    public function show($slug)
    {
        $club = Club::where('slug', $slug)
            ->with(['sport', 'opportunites' => fn($q) => $q->latest()->take(5)])
            ->firstOrFail();
        return view('clubs.show', compact('club'));
    }
}