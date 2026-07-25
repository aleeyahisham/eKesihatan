@extends('layouts.app')

@section('content')
<h2>Patient History</h2>
<p>{{ $patient->name }}</p>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Doctor</th>
            <th>Service</th>
            <th>Status</th>
            <th>Attachment</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($appointments as $appointment)
            <tr>
                <td>{{ $appointment->scheduled_at->format('d M Y') }}</td>
                <td>{{ $appointment->doctor->name }}</td>
                <td>{{ $appointment->service?->name ?? 'General' }}</td>
                <td>{{ ucfirst($appointment->status) }}</td>
                <td>
                    <div class="table-actions">
                        @forelse ($appointment->documents as $document)
                            <a class="button-link secondary" href="{{ route('doctor.documents.show', $document) }}" target="_blank">View Attachment</a>
                        @empty
                            <span class="button-link secondary disabled">View Attachment</span>
                        @endforelse
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No appointment history.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection