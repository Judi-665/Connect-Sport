<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MediaReactionController extends Controller
{
    public function toggle(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'reaction' => ['required', Rule::in(['like', 'love'])],
        ]);

        $reaction = DB::transaction(function () use ($media, $request, $validated) {
            $lockedMedia = Media::query()
                ->whereKey($media->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($lockedMedia->visibilite === 'public' && !$lockedMedia->payant, 404);

            $existing = $lockedMedia->reactions()
                ->where('user_id', $request->user()->id)
                ->first();

            if ($existing?->type === $validated['reaction']) {
                $existing->delete();
                return null;
            }

            if ($existing) {
                $existing->update(['type' => $validated['reaction']]);
            } else {
                $lockedMedia->reactions()->create([
                    'user_id' => $request->user()->id,
                    'type' => $validated['reaction'],
                ]);
            }

            return $validated['reaction'];
        });

        if ($request->expectsJson()) {
            return response()->json([
                'reaction' => $reaction,
                'likes_count' => $media->reactions()->where('type', 'like')->count(),
                'loves_count' => $media->reactions()->where('type', 'love')->count(),
            ]);
        }

        return back();
    }
}