@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Available Health Services</h2>
        <p>Manage the services available for patient booking.</p>
    </div>
    <a class="button-link" href="{{ route('admin.services.create') }}">Add Service</a>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Duration</th>
                <th>Status</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
                <tr>
                    <td>
                        <div class="service-name-group">
                            <strong>{{ $service->name }}</strong>
                            <span class="service-badge">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </td>
                    <td>{{ $service->description ?: 'No description provided.' }}</td>
                    <td>{{ $service->duration_minutes }} mins</td>
                    <td>{{ $service->is_active ? 'Visible to patients' : 'Hidden from patients' }}</td>
                    <td>
                        <div class="admin-actions">
                            <a class="button-link secondary" href="{{ route('admin.services.edit', $service) }}">Edit</a>
                            <form
                                action="{{ route('admin.services.destroy', $service) }}"
                                method="POST"
                                data-confirm-message="Delete this health service? Existing appointments may be affected."
                            >
                                @csrf
                                @method('DELETE')
                                <button class="button-link danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No services created.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection