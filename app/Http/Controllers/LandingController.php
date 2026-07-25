<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\DownloadableForm;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        $bulletins = Schema::hasTable('bulletins')
            ? Bulletin::query()
                ->where('is_published', true)
                ->orderByDesc('event_date')
                ->latest()
                ->get()
            : collect();

        $downloadableForms = Schema::hasTable('downloadable_forms')
            ? DownloadableForm::query()
                ->where('is_published', true)
                ->whereNotNull('file_path')
                ->orderBy('sort_order')
                ->latest()
                ->get()
            : collect();

        $this->synchronisePublicFiles($bulletins, $downloadableForms);

        return view('landing', [
            'bulletins' => $bulletins,
            'downloadableForms' => $downloadableForms,
        ]);
    }

    private function synchronisePublicFiles(
        Collection $bulletins,
        Collection $downloadableForms
    ): void {
        $livePublicPath = $this->livePublicPath();

        if ($livePublicPath === public_path()) {
            return;
        }

        foreach ($bulletins as $bulletin) {
            $this->copyToLivePublic($bulletin->poster_path, $livePublicPath);
        }

        foreach ($downloadableForms as $form) {
            $this->copyToLivePublic($form->file_path, $livePublicPath);
        }
    }

    private function copyToLivePublic(?string $relativePath, string $livePublicPath): void
    {
        $relativePath = $this->normaliseRelativePath($relativePath);

        if ($relativePath === null) {
            return;
        }

        $source = public_path($relativePath);
        $destination = $livePublicPath . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        if (!File::isFile($source) || File::isFile($destination)) {
            return;
        }

        try {
            File::ensureDirectoryExists(dirname($destination), 0755, true);
            File::copy($source, $destination);
        } catch (\Throwable $exception) {
            Log::warning('Unable to synchronise a public landing-page file.', [
                'relative_path' => $relativePath,
                'source' => $source,
                'destination' => $destination,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function livePublicPath(): string
    {
        $candidate = dirname(base_path()) . DIRECTORY_SEPARATOR . 'public_html';

        return File::isDirectory($candidate) ? $candidate : public_path();
    }

    private function normaliseRelativePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (
            str_contains($path, '..') ||
            (!str_starts_with($path, 'images/bulletins/')
                && !str_starts_with($path, 'downloads/forms/'))
        ) {
            return null;
        }

        return $path;
    }
}
