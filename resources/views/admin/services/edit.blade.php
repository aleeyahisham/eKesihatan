@extends('layouts.app')
 
@section('content')
<div class="page-header">
    <div>
        <h2>Edit Health Service</h2>
        <p>Update service details, duration, and visibility.</p>
    </div>
    <a class="button-link secondary" href="{{ route('admin.services.index') }}">Back to Services</a>
</div>

<section class="admin-page-card">
    <form class="auth-form profile-form" method="POST" action="{{ route('admin.services.update', $service) }}">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Service Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $service->name) }}" required>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ old('description', $service->description) }}</textarea>
        </div>
        <div>
            <label for="duration_minutes">Duration (minutes)</label>
            <input id="duration_minutes" name="duration_minutes" type="number" min="5" max="240" value="{{ old('duration_minutes', $service->duration_minutes) }}" required>
        </div>
        <div>
            <input type="hidden" name="is_active" value="0">
            <label for="is_active">
                <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $service->is_active))>
                Active
            </label>
        </div>
        <button type="submit">Update Service</button>
    </form>
</section>
@endsection