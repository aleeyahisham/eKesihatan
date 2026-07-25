@extends('layouts.app')

@section('content')
<h2>BMI Calculator</h2>

<form method="POST" action="{{ route('patient.bmi.calculate') }}">
    @csrf
    <div>
        <label for="sex">Gender</label>
        <select id="sex" name="sex" required>
            <option value="male" @selected(old('sex', $sex ?? '') === 'male')>Male</option>
            <option value="female" @selected(old('sex', $sex ?? '') === 'female')>Female</option>
            <option value="other" @selected(old('sex', $sex ?? '') === 'other')>Other</option>
        </select>
    </div>
    <div>
        <label for="age">Age</label>
        <input id="age" name="age" type="number" min="1" max="120" value="{{ old('age', $age ?? '') }}" required>
    </div>
    <div>
        <label for="height_cm">Height (cm)</label>
        <input id="height_cm" name="height_cm" type="number" step="0.1" value="{{ old('height_cm', $height_cm ?? '') }}" required>
    </div>
    <div>
        <label for="weight_kg">Weight (kg)</label>
        <input id="weight_kg" name="weight_kg" type="number" step="0.1" value="{{ old('weight_kg', $weight_kg ?? '') }}" required>
    </div>
    <button type="submit">Calculate BMI</button>
</form>

@isset($bmi)
    <div>
        <p><strong>Your BMI:</strong> {{ $bmi }}</p>
        <p><strong>Category:</strong> {{ $category }}</p>
        <p><strong>Age:</strong> {{ $age }}</p>
        <p><strong>Gender:</strong> {{ ucfirst($sex) }}</p>
    </div>
@endisset
@endsection