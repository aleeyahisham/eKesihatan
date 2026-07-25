@extends('layouts.app')

@section('content')
<div class="page-header service-page-header">
    <div>
        <h2>Available Health Services</h2>
        <p>Choose a care option below and book your visit in a few steps.</p>
    </div>
</div>

<p class="service-intro">These are the clinic services currently available for booking at the university health centre. Each option includes a short description so you can select the right care before confirming your appointment.</p>

<div class="service-table-card">
    <table class="service-table table-card-mobile">
        <thead>
            <tr>
                <th>Service</th>
                <th>What it covers</th>
                <th>Approx. time</th>
                <th class="service-action-header">Book</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $service)
                <tr>
                    <td data-label="Service">
                        <div class="service-name-group">
                            <strong>{{ $service->name }}</strong>
                            <span class="service-badge">Available now</span>
                        </div>
                    </td>
                    <td data-label="What it covers">{{ $service->description }}</td>
                    <td data-label="Approx. time">{{ $service->duration_minutes }} mins</td>
                    <td class="service-action-cell" data-label="Book">
                        <a class="service-book-link" href="{{ route('patient.appointments.create', ['service_id' => $service->id]) }}">Book now</a>
                    </td>
                </tr>
            @empty
                <tr><td class="table-empty" colspan="4">No active services.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<section class="doctor-gallery">
    <div class="section-header">
        <div>
            <h3>Meet the doctors</h3>
            <p>Each doctor supports one of our main care services so you can choose confidently.</p>
        </div>
    </div>

    <div class="doctor-grid">
        @forelse ($doctors as $doctor)
            <article class="doctor-card">
                <img src="{{ $doctor->image_url }}" alt="{{ $doctor->name }}">
                <div>
                    <h4>{{ $doctor->name }}</h4>
                    <p class="doctor-specialty">{{ $doctor->specialization ?? 'Clinic doctor' }}</p>
                    <p>{{ $doctor->service_focus ?? 'Available for clinic care.' }}</p>
                </div>
            </article>
        @empty
            <p>No doctors are currently listed.</p>
        @endforelse
    </div>
</section>
@endsection