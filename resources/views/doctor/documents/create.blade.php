@extends('layouts.app')
 
@section('content')
<h2 data-i18n="Upload Medical Document">Upload Medical Document</h2>
<p><strong data-i18n="Patient:">Patient:</strong> {{ $appointment->patient->name }}</p>
 
<form method="POST" action="{{ route('doctor.documents.store', $appointment) }}" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="document_type" data-i18n="Document Type">Document Type</label>
        <input id="document_type" name="document_type" type="text" placeholder="Medical certificate or referral letter">
    </div>
    <div>
        <label for="document" data-i18n="Select PDF or JPG File">Select PDF or JPG File</label>
        <input id="document" name="document" type="file" accept=".pdf,.jpg,.jpeg" required>
    </div>
    <button type="submit" data-i18n="Upload">Upload</button>
</form>
@endsection