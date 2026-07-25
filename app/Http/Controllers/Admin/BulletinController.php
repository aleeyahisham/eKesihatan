<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BulletinController extends Controller
{
    public function index()
    {
        return view('admin.bulletins.index', [
            'bulletins' => Bulletin::query()->latest('event_date')->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.bulletins.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateBulletin($request);
        $data['is_published'] = $request->boolean('is_published');
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('poster')) {
            $data['poster_path'] = $this->storePoster($request->file('poster'));
        }

        Bulletin::create($data);

        return redirect()->route('admin.bulletins.index')->with('status', 'Bulletin created.');
    }

    public function edit(Bulletin $bulletin)
    {
        return view('admin.bulletins.edit', [
            'bulletin' => $bulletin,
        ]);
    }

    public function update(Request $request, Bulletin $bulletin)
    {
        $data = $this->validateBulletin($request);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->boolean('remove_poster')) {
            $this->deletePoster($bulletin->poster_path);
            $data['poster_path'] = null;
        }

        if ($request->hasFile('poster')) {
            $this->deletePoster($bulletin->poster_path);
            $data['poster_path'] = $this->storePoster($request->file('poster'));
        }

        $bulletin->update($data);

        return redirect()->route('admin.bulletins.index')->with('status', 'Bulletin updated.');
    }

    public function destroy(Bulletin $bulletin)
    {
        $this->deletePoster($bulletin->poster_path);
        $bulletin->delete();

        return redirect()->route('admin.bulletins.index')->with('status', 'Bulletin removed.');
    }

    private function validateBulletin(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'details' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'event_time' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
            'remove_poster' => ['nullable', 'boolean'],
            'poster' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);
    }

    private function storePoster($poster): string
    {
        $directory = public_path('images/bulletins');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $filename = Str::uuid()->toString() . '.' . strtolower($poster->getClientOriginalExtension());
        $poster->move($directory, $filename);

        return 'images/bulletins/' . $filename;
    }

    private function deletePoster(?string $path): void
    {
        if (!$path || !str_starts_with($path, 'images/bulletins/')) {
            return;
        }

        $absolutePath = public_path($path);

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
