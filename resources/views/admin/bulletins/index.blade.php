@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2 data-i18n="Clinic Bulletins">Clinic Bulletins</h2>
        <p>Publish and manage clinic announcements shown to users.</p>
    </div>
    <a class="button-link" href="{{ route('admin.bulletins.create') }}" data-i18n="Add Bulletin">Add Bulletin</a>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
    <thead>
        <tr>
            <th data-i18n="Title">Title</th>
            <th data-i18n="Date">Date</th>
            <th data-i18n="Status">Status</th>
            <th data-i18n="Actions" style="text-align: right;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($bulletins as $bulletin)
            <tr>
                <td>{{ $bulletin->title }}</td>
                <td>{{ $bulletin->event_date ? $bulletin->event_date->format('d M Y') : '—' }}</td>
                <td data-i18n="{{ $bulletin->is_published ? 'Published' : 'Draft' }}">{{ $bulletin->is_published ? 'Published' : 'Draft' }}</td>
                <td>
                    <div class="admin-actions">
                        <a class="button-link secondary" href="{{ route('admin.bulletins.edit', $bulletin) }}" data-i18n="Edit">Edit</a>
                        <form
                            action="{{ route('admin.bulletins.destroy', $bulletin) }}"
                            method="POST"
                            data-confirm-message="Delete this bulletin post? This will remove it from the landing page."
                        >
                            @csrf
                            @method('DELETE')
                            <button class="button-link danger" type="submit" data-i18n="Delete">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" data-i18n="No bulletins created.">No bulletins created.</td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>
@endsection
