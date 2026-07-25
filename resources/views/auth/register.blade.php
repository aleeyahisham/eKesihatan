@extends('layouts.app')

@section('content')
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-card__header">
            <h2 data-i18n="Patient Registration">Patient Registration</h2>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="auth-form auth-form--register" novalidate>
            @csrf
            <div class="auth-form__grid auth-form__grid--wide">
                <div class="auth-field auth-field--wide">
                    <label for="name" data-i18n="Full Name (per IC)">Full Name (per IC)</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name" inputmode="text" aria-describedby="name-help">
                    @error('name')
                        <div class="field-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>
                <div class="auth-field">
                    <label for="student_id" data-i18n="Student ID">Student ID</label>
                    <input id="student_id" name="student_id" type="text" inputmode="numeric" pattern="[0-9]*" value="{{ old('student_id') }}" required autocomplete="off" aria-describedby="student-id-help">
                    @error('student_id')
                        <div class="field-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="auth-form__grid auth-form__grid--wide">
                <div class="auth-field auth-field--wide">
                    <label for="email" data-i18n="Email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" aria-describedby="email-help">
                    @error('email')
                        <div class="field-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>
                <div class="auth-field">
                    <label for="phone_number" data-i18n="Phone Number">Phone Number</label>
                    <input id="phone_number" name="phone_number" type="text" inputmode="numeric" pattern="[0-9]*" value="{{ old('phone_number') }}" required autocomplete="tel" aria-describedby="phone-help">
                    @error('phone_number')
                        <div class="field-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="auth-form__grid">
                <div class="auth-field">
                    <label for="password" data-i18n="Password">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" aria-describedby="password-help">
                    <div class="field-help" id="password-help" data-i18n="Minimum 8 characters.">Minimum 8 characters.</div>
                    <div class="password-meter" aria-live="polite">
                        <span class="password-meter__bar" id="password-meter-bar"></span>
                    </div>
                    <div class="field-help password-strength" id="password-strength-text" data-i18n="Password strength">Password strength</div>
                    @error('password')
                        <div class="field-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>
                <div class="auth-field">
                    <label for="password_confirmation" data-i18n="Confirm Password">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">
                    @error('password_confirmation')
                        <div class="field-error" role="alert">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button type="submit" data-i18n="Register">Register</button>
        </form>

        <div class="auth-footer">
            <span data-i18n="Already have an account?">Already have an account?</span>
            <a href="{{ route('login') }}" data-i18n="Login">Login</a>
        </div>
    </div>
</div>

<script>
    (function () {
        const nameInput = document.getElementById('name');
        const studentIdInput = document.getElementById('student_id');
        const phoneInput = document.getElementById('phone_number');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const passwordMeter = document.getElementById('password-meter-bar');
        const passwordStrengthText = document.getElementById('password-strength-text');
        const form = document.querySelector('.auth-form');
        let emailWasManuallyEdited = false;

        const setFieldError = (input, message) => {
            const field = input.closest('.auth-field');
            let errorNode = field.querySelector('.field-error');
            if (!errorNode) {
                errorNode = document.createElement('div');
                errorNode.className = 'field-error';
                errorNode.setAttribute('role', 'alert');
                field.appendChild(errorNode);
            }
            errorNode.textContent = message;
            input.classList.toggle('is-invalid', Boolean(message));
        };

        const clearFieldError = (input) => {
            const field = input.closest('.auth-field');
            const errorNode = field.querySelector('.field-error');
            if (errorNode) {
                errorNode.remove();
            }
            input.classList.remove('is-invalid');
        };

        const enforceNumeric = (input) => {
            input.value = input.value.replace(/[^0-9]/g, '');
        };

        const validateName = () => {
            if (!nameInput.value.trim()) {
                setFieldError(nameInput, 'Full name is required.');
            } else if (/[0-9]/.test(nameInput.value)) {
                setFieldError(nameInput, 'Full name must not contain numbers.');
            } else {
                clearFieldError(nameInput);
            }
        };

        const sanitizeNameInput = () => {
            const sanitized = nameInput.value.replace(/\d/g, '');
            if (sanitized !== nameInput.value) {
                nameInput.value = sanitized;
            }
        };

        const validateStudentId = () => {
            const studentId = studentIdInput.value.trim();
            if (!studentId) {
                setFieldError(studentIdInput, 'Student ID is required.');
            } else if (!/^\d+$/.test(studentId)) {
                setFieldError(studentIdInput, 'Student ID must contain numbers only.');
            } else {
                clearFieldError(studentIdInput);
            }
        };

        const validatePhoneNumber = () => {
            const phone = phoneInput.value.trim();
            if (!phone) {
                setFieldError(phoneInput, 'Phone number is required.');
            } else if (!/^\d+$/.test(phone)) {
                setFieldError(phoneInput, 'Phone number must contain numbers only.');
            } else {
                clearFieldError(phoneInput);
            }
        };

        const validatePassword = () => {
            const value = passwordInput.value;
            let strength = 'Weak';
            let width = 25;
            let barClass = '';

            if (value.length >= 8 && /[A-Z]/.test(value) && /[0-9]/.test(value)) {
                strength = 'Strong';
                width = 100;
                barClass = 'is-good';
            } else if (value.length >= 8) {
                strength = 'Good';
                width = 70;
                barClass = 'is-medium';
            } else if (value.length >= 4) {
                strength = 'Fair';
                width = 45;
            }

            if (passwordMeter) {
                passwordMeter.style.width = `${width}%`;
                passwordMeter.className = `password-meter__bar ${barClass}`.trim();
            }
            if (passwordStrengthText) {
                passwordStrengthText.textContent = `Password strength: ${strength}`;
            }

            if (value.length < 8) {
                setFieldError(passwordInput, 'Password must be at least 8 characters.');
            } else {
                clearFieldError(passwordInput);
            }

            if (passwordConfirmationInput.value && passwordConfirmationInput.value !== value) {
                setFieldError(passwordConfirmationInput, 'Passwords do not match.');
            } else {
                clearFieldError(passwordConfirmationInput);
            }
        };

        const validateEmail = () => {
            const studentId = studentIdInput.value.trim();
            const currentEmail = emailInput.value.trim().toLowerCase();
            const expectedEmail = `${studentId}@student.uitm.edu.my`;

            if (!currentEmail) {
                if (studentId) {
                    emailInput.value = expectedEmail;
                }
                clearFieldError(emailInput);
                return;
            }

            if (!/^[0-9]+@student\.uitm\.edu\.my$/i.test(currentEmail)) {
                setFieldError(emailInput, 'Email must use the format 2023415142@student.uitm.edu.my.');
            } else if (studentId && currentEmail !== expectedEmail.toLowerCase()) {
                setFieldError(emailInput, `Email must match your student ID as ${expectedEmail}.`);
            } else {
                clearFieldError(emailInput);
            }
        };

        const validateForm = () => {
            validateName();
            validateStudentId();
            validatePhoneNumber();
            validateEmail();
            validatePassword();
            return !document.querySelector('.auth-form .field-error');
        };

        [studentIdInput, phoneInput].forEach((input) => {
            input.addEventListener('input', () => {
                enforceNumeric(input);
                if (input === studentIdInput) {
                    const studentId = studentIdInput.value.trim();
                    if (studentId && !emailWasManuallyEdited) {
                        emailInput.value = `${studentId}@student.uitm.edu.my`;
                    }
                    validateEmail();
                } else {
                    validatePhoneNumber();
                }
            });
        });

        [nameInput, emailInput, passwordInput, passwordConfirmationInput].forEach((input) => {
            input.addEventListener('input', () => {
                if (input === nameInput) {
                    sanitizeNameInput();
                    validateName();
                }

                if (input === emailInput) {
                    emailWasManuallyEdited = true;
                    validateEmail();
                }

                if (input === passwordInput || input === passwordConfirmationInput) {
                    validatePassword();
                }
            });
        });

        passwordInput.addEventListener('blur', validatePassword);
        passwordConfirmationInput.addEventListener('blur', validatePassword);
        emailInput.addEventListener('blur', validateEmail);
        nameInput.addEventListener('blur', validateName);
        nameInput.addEventListener('keydown', (event) => {
            if (/\d/.test(event.key)) {
                event.preventDefault();
            }
        });
        studentIdInput.addEventListener('blur', validateStudentId);
        phoneInput.addEventListener('blur', validatePhoneNumber);

        if (form) {
            form.addEventListener('submit', (event) => {
                if (!validateForm()) {
                    event.preventDefault();
                }
            });
        }
    })();
</script>
@endsection