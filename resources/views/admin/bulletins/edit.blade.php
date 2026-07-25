@extends('layouts.app')

@section('content')
<div class="page-header">
    <div>
        <h2 data-i18n="Edit Bulletin">Edit Bulletin</h2>
        <p>Update bulletin details and publication settings.</p>
    </div>
    <a class="button-link secondary" href="{{ route('admin.bulletins.index') }}">Back to Bulletins</a>
</div>

<section class="admin-page-card">
<form class="auth-form profile-form" method="POST" action="{{ route('admin.bulletins.update', $bulletin) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div>
        <label for="title" data-i18n="Bulletin Title">Bulletin Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $bulletin->title) }}" required>
    </div>

    <div>
        <label for="summary" data-i18n="Summary">Summary</label>
        <textarea id="summary" name="summary" rows="3">{{ old('summary', $bulletin->summary) }}</textarea>
    </div>

    <div>
        <label for="details" data-i18n="Program Details">Program Details</label>
        <textarea id="details" name="details" rows="5">{{ old('details', $bulletin->details) }}</textarea>
    </div>

    <div>
        <label for="event_date" data-i18n="Event Date">Event Date</label>
        <input id="event_date" name="event_date" type="date" value="{{ old('event_date', optional($bulletin->event_date)->format('Y-m-d')) }}">
    </div>

    <div>
        <label for="event_time" data-i18n="Event Time">Event Time</label>
        <input id="event_time" name="event_time" type="text" value="{{ old('event_time', $bulletin->event_time) }}" placeholder="10:00 AM - 5:00 PM">
    </div>

    <div>
        <label for="location" data-i18n="Location">Location</label>
        <input id="location" name="location" type="text" value="{{ old('location', $bulletin->location) }}">
    </div>

    @if ($bulletin->poster_path)
        <div>
            <label data-i18n="Current Poster">Current Poster</label>
            <img src="{{ asset($bulletin->poster_path) }}" alt="Current bulletin poster" class="admin-bulletin-poster-preview">
        </div>
        <div>
            <label for="remove_poster" data-i18n="Remove current poster">Remove current poster</label>
            <input id="remove_poster" name="remove_poster" type="checkbox" value="1">
        </div>
    @endif

    <div>
        <label for="poster" data-i18n="Poster Image (JPG, JPEG, PNG)">Poster Image (JPG, JPEG, PNG)</label>
        <input id="poster" name="poster" type="file" accept=".jpg,.jpeg,.png">
    </div>

    <div>
        <label for="is_published" data-i18n="Publish on Landing Page">Publish on Landing Page</label>
        <input id="is_published" name="is_published" type="checkbox" value="1" @checked(old('is_published', $bulletin->is_published))>
    </div>

    <button type="submit" data-i18n="Update Bulletin">Update Bulletin</button>
</form>
</section>
@endsection
