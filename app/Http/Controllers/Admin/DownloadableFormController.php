<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadableForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DownloadableFormController extends Controller
{
    public function index()
    {
        $isTableMissing = $this->isDownloadableFormsTableMissing();

        return view('admin.forms.index', [
            'forms' => $isTableMissing
                ? collect()
                : DownloadableForm::query()
                    ->orderBy('sort_order')
                    ->latest()
                    ->get(),
            'downloadableFormsTableMissing' => $isTableMissing,
        ]);
    }

    public function create()
    {
        if ($this->isDownloadableFormsTableMissing()) {
            return redirect()
                ->route('admin.forms.index')
                ->with('status', 'Forms table is missing. Run php artisan migrate first.');
        }

        return view('admin.forms.create');
    }

    public function store(Request $request)
    {
        if ($this->isDownloadableFormsTableMissing()) {
            return redirect()
                ->route('admin.forms.index')
                ->with('status', 'Forms table is missing. Run php artisan migrate first.');
        }

        $data = $this->validateForm($request, false);
        $data['is_published'] = $request->boolean('is_published');
        $data['created_by'] = $request->user()->id;
        $data['file_path'] = $this->storeFormFile($request->file('form_file'));

        DownloadableForm::create($data);

        return redirect()
            ->route('admin.forms.index')
            ->with('status', 'Form created successfully.');
    }

    public function edit(DownloadableForm $form)
    {
        if ($this->isDownloadableFormsTableMissing()) {
            return redirect()
                ->route('admin.forms.index')
                ->with('status', 'Forms table is missing. Run php artisan migrate first.');
        }

        return view('admin.forms.edit', compact('form'));
    }

    public function update(Request $request, DownloadableForm $form)
    {
        if ($this->isDownloadableFormsTableMissing()) {
            return redirect()
                ->route('admin.forms.index')
                ->with('status', 'Forms table is missing. Run php artisan migrate first.');
        }

        $data = $this->validateForm($request, true);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('form_file')) {
            $newPath = $this->storeFormFile($request->file('form_file'));
            $oldPath = $form->file_path;
            $data['file_path'] = $newPath;
            $form->update($data);
            $this->deleteFormFile($oldPath);
        } else {
            $data['file_path'] = $form->file_path;
            $form->update($data);
        }

        return redirect()
            ->route('admin.forms.index')
            ->with('status', 'Form updated successfully.');
    }

    public function destroy(DownloadableForm $form)
    {
        if ($this->isDownloadableFormsTableMissing()) {
            return redirect()
                ->route('admin.forms.index')
                ->with('status', 'Forms table is missing. Run php artisan migrate first.');
        }

        $path = $form->file_path;
        $form->delete();
        $this->deleteFormFile($path);

        return redirect()
            ->route('admin.forms.index')
            ->with('status', 'Form removed.');
    }

    private function validateForm(Request $request, bool $isUpdate): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
            'form_file' => [
                $isUpdate ? 'nullable' : 'required',
                'file',
                'max:10240',
            ],
        ]);

        if ($request->hasFile('form_file')) {
            $extension = strtolower(
                $request->file('form_file')->getClientOriginalExtension()
            );

            $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

            if (!in_array($extension, $allowed, true)) {
                throw ValidationException::withMessages([
                    'form_file' => 'Upload a PDF, Word, Excel, or PowerPoint file.',
                ]);
            }
        }

        return $data;
    }

    private function storeFormFile($file): string
    {
        if (!$file || !$file->isValid()) {
            throw ValidationException::withMessages([
                'form_file' => 'The uploaded file is not valid. Please choose it again.',
            ]);
        }

        $relativeDirectory = 'downloads/forms';
        $directory = $this->livePublicPath()
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativeDirectory);

        File::ensureDirectoryExists($directory, 0755, true);

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid()->toString() . '.' . $extension;
        $destination = $directory . DIRECTORY_SEPARATOR . $filename;

        try {
            $file->move($directory, $filename);
        } catch (\Throwable $exception) {
            Log::error('Downloadable form upload failed.', [
                'destination' => $destination,
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'form_file' => 'The server could not save the file. Please try again.',
            ]);
        }

        if (!File::isFile($destination)) {
            throw ValidationException::withMessages([
                'form_file' => 'The file was not saved successfully.',
            ]);
        }

        return $relativeDirectory . '/' . $filename;
    }

    private function deleteFormFile(?string $path): void
    {
        $path = $this->normaliseFormPath($path);

        if ($path === null) {
            return;
        }

        foreach (array_unique([$this->livePublicPath(), public_path()]) as $root) {
            $absolutePath = $root . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $path);

            if (File::isFile($absolutePath)) {
                File::delete($absolutePath);
            }
        }
    }

    private function livePublicPath(): string
    {
        $candidate = dirname(base_path()) . DIRECTORY_SEPARATOR . 'public_html';

        return File::isDirectory($candidate) ? $candidate : public_path();
    }

    private function normaliseFormPath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        if (
            str_contains($path, '..') ||
            !str_starts_with($path, 'downloads/forms/')
        ) {
            return null;
        }

        return $path;
    }

    private function isDownloadableFormsTableMissing(): bool
    {
        return !Schema::hasTable('downloadable_forms');
    }
}
