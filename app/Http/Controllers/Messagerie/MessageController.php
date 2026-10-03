<?php

namespace App\Http\Controllers\Messagerie;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function inbox()
    {
        /** @var User $user */
        $user = Auth::user();

        $conversations = Message::where('destinataire_id', $user->id)
                                ->orWhere('expediteur_id', $user->id)
                                ->nonArchive($user->id)
                                ->with(['expediteur', 'destinataire'])
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->groupBy(function (Message $msg) use ($user) {
                                    return $msg->expediteur_id === $user->id
                                        ? $msg->destinataire_id
                                        : $msg->expediteur_id;
                                });

        return view('messages.inbox', compact('conversations'));
    }

    public function conversation(User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $messages = Message::conversation($currentUser->id, $user->id)
                           ->with(['expediteur', 'destinataire'])
                           ->get();

        Message::where('destinataire_id', $currentUser->id)
               ->where('expediteur_id', $user->id)
               ->nonLus()
               ->get()
               ->each(function (Message $msg) {
                   $msg->marquerLu();
               });

        return view('messages.conversation', compact('user', 'messages'));
    }

    public function send(Request $request)
    {
        abort_if(Auth::user()->role === 'supporter', 403, 'Les supporters ne peuvent pas envoyer de messages.');

        $validated = $request->validate([
            'destinataire_id' => 'required|exists:users,id|different:' . Auth::id(),
            'contenu'         => 'required|string|max:2000',
            'club_id'         => 'nullable|exists:clubs,id',
        ]);

        $message = Message::create(array_merge($validated, [
            'expediteur_id' => Auth::id(),
        ]));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('expediteur', 'destinataire'),
            ]);
        }

        return back()->with('success', 'Message envoyé.');
    }

    public function nonLus()
    {
        /** @var User $user */
        $user = Auth::user();

        $messages = Message::where('destinataire_id', $user->id)
                           ->nonLus()
                           ->with('expediteur')
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);

        return view('messages.non-lus', compact('messages'));
    }

    public function markAsRead(Message $message)
    {
        $this->authorize('view', $message);
        $message->marquerLu();

        return back();
    }

    public function markAllAsRead()
    {
        /** @var User $user */
        $user = Auth::user();

        Message::where('destinataire_id', $user->id)
               ->nonLus()
               ->get()
               ->each(function (Message $msg) {
                   $msg->marquerLu();
               });

        return back()->with('success', 'Tous les messages marqués comme lus.');
    }

    public function archive(Message $message)
    {
        $this->authorize('view', $message);

        /** @var User $user */
        $user = Auth::user();

        if ($message->expediteur_id === $user->id) {
            $message->update(['archive_expediteur' => true]);
        } else {
            $message->update(['archive_destinataire' => true]);
        }

        return back()->with('success', 'Message archivé.');
    }

    public function archives()
    {
        /** @var User $user */
        $user = Auth::user();

        $messages = Message::where(function ($q) use ($user) {
                        $q->where('expediteur_id', $user->id)
                          ->where('archive_expediteur', true);
                    })
                    ->orWhere(function ($q) use ($user) {
                        $q->where('destinataire_id', $user->id)
                          ->where('archive_destinataire', true);
                    })
                    ->with(['expediteur', 'destinataire'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        return view('messages.archives', compact('messages'));
    }

    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        $message->delete();

        return back()->with('success', 'Message supprimé.');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $messages = Message::where(function ($q) use ($user) {
                        $q->where('expediteur_id', $user->id)
                          ->orWhere('destinataire_id', $user->id);
                    })
                    ->where('contenu', 'like', '%' . $validated['q'] . '%')
                    ->with(['expediteur', 'destinataire'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        $query = $validated['q'];

        return view('messages.search', compact('messages', 'query'));
    }
}