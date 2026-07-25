<?php
 
namespace App\Http\Controllers\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\HealthService;
use Illuminate\Http\Request;
 
class HealthServiceController extends Controller
{
    public function index()
    {
        return view('admin.services.index', [
            'services' => HealthService::orderBy('name')->get(),
        ]);
    }
 
    public function create()
    {
        return view('admin.services.create');
    }
 
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:240'],
            'is_active' => ['nullable', 'boolean'],
        ]);
 
        $data['is_active'] = $request->boolean('is_active');
        HealthService::create($data);
 
        return redirect()->route('admin.services.index')->with('status', 'Health service created.');
    }
 
    public function edit(HealthService $service)
    {
        return view('admin.services.edit', [
            'service' => $service,
        ]);
    }
 
    public function update(Request $request, HealthService $service)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:240'],
            'is_active' => ['nullable', 'boolean'],
        ]);
 
        $data['is_active'] = $request->boolean('is_active');
        $service->update($data);
 
        return redirect()->route('admin.services.index')->with('status', 'Health service updated.');
    }
 
    public function destroy(HealthService $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('status', 'Health service removed.');
    }
}