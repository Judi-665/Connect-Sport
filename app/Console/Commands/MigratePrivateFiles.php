<?php

namespace App\Console\Commands;

use App\Models\Licence;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class MigratePrivateFiles extends Command
{
    protected $signature = 'security:migrate-private-files';

    protected $description = 'Move existing club media and licence files off the public disk';

    public function handle(): int
    {
        $moved = 0;
        $skipped = 0;
        $failed = 0;

        Media::withTrashed()->orderBy('id')->each(function (Media $media) use (&$moved, &$skipped, &$failed): void {
            foreach (['chemin', 'miniature'] as $attribute) {
                $path = $media->{$attribute};
                if (!$path) {
                    continue;
                }

                $result = $this->moveFile($path, "medias/{$media->club_id}/", 'media_private');
                $result === 'moved' ? $moved++ : ($result === 'skipped' ? $skipped++ : $failed++);
            }
        });

        Licence::orderBy('id')->each(function (Licence $licence) use (&$moved, &$skipped, &$failed): void {
            $result = $this->moveFile(
                $licence->fichier_pdf,
                "licences/{$licence->club_id}/",
                'licence_private'
            );
            $result === 'moved' ? $moved++ : ($result === 'skipped' ? $skipped++ : $failed++);
        });

        $this->info("Déplacés : {$moved} ; déjà privés/absents : {$skipped} ; erreurs : {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function moveFile(string $path, string $requiredPrefix, string $privateDisk): string
    {
        if (!str_starts_with($path, $requiredPrefix) || str_contains($path, '..') || str_contains($path, chr(92))) {
            $this->error("Chemin refusé : {$path}");
            return 'failed';
        }

        $public = Storage::disk('public');
        $private = Storage::disk($privateDisk);

        try {
            if (!$public->exists($path)) {
                return 'skipped';
            }

            if (!$private->exists($path)) {
                $stream = $public->readStream($path);
                if (!is_resource($stream)) {
                    throw new RuntimeException('Impossible de lire le fichier source.');
                }

                try {
                    if (!$private->writeStream($path, $stream)) {
                        throw new RuntimeException('Échec de copie vers le stockage privé.');
                    }
                } finally {
                    fclose($stream);
                }
            }

            if (!$public->delete($path)) {
                throw new RuntimeException('Copie privée réussie mais suppression publique échouée.');
            }

            return 'moved';
        } catch (Throwable $exception) {
            $this->error("{$path} : {$exception->getMessage()}");
            return 'failed';
        }
    }
}
