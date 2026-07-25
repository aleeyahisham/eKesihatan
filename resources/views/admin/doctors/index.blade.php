@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2>Doctors</h2>
        <p>Manage doctor records, profile details, and specialization focus.</p>
    </div>
    <a class="button-link" href="{{ route('admin.doctors.create') }}">Add Doctor</a>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Specialization</th>
            <th>Service Focus</th>
            <th>Phone</th>
            <th style="text-align: right;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($doctors as $doctor)
            <tr>
                <td>{{ $doctor->name }}</td>
                <td>{{ $doctor->email }}</td>
                <td>{{ $doctor->specialization ?? 'General' }}</td>
                <td>{{ $doctor->getAttribute('service_focus') ?? 'Clinic care' }}</td>
                <td>{{ $doctor->phone_number ?? '-' }}</td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="{{ route('admin.doctors.edit', $doctor) }}">Edit</a>
                        <form
                            action="{{ route('admin.doctors.destroy', $doctor) }}"
                            method="POST"
                            data-confirm-message="Remove this doctor profile? This action cannot be undone."
                        >
                            @csrf
                            @method('DELETE')
                            <button class="button-link danger" type="submit">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No doctors added.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection