<?php $__env->startSection('content'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const serviceSelect = document.getElementById('health_service_id');
        const doctorSelect = document.getElementById('doctor_id');
        const doctorOptionsByService = <?php echo json_encode($services->mapWithKeys(function ($service) {
            return [$service->id => \App\Models\User::doctorsForService($service->name)->map(function ($doctor) {
                return ['value' => (string) $doctor->id, 'label' => $doctor->name . ' (' . ($doctor->specialization ?? 'General') . ')'];
            })->values()->all()];
        })->all(), 512) ?>;

        function renderDoctors(serviceId) {
            const doctors = doctorOptionsByService[serviceId] || [];
            const currentValue = doctorSelect.value;
            doctorSelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = doctors.length ? 'Any available doctor' : 'No specialist doctors available for this service';
            doctorSelect.appendChild(placeholder);

            doctors.forEach(function (doctor) {
                const option = document.createElement('option');
                option.value = doctor.value;
                option.textContent = doctor.label;
                if (currentValue === doctor.value) {
                    option.selected = true;
                }
                doctorSelect.appendChild(option);
            });
        }

        const dateInput = document.getElementById('preferred_date');
        const timeInput = document.getElementById('preferred_time');
        const assistantMessage = document.getElementById('assistant-message');
        const recommendationList = document.getElementById('assistant-recommendations');
        const recommendButton = document.getElementById('assistant-recommend-btn');
        const selectedSlotInput = document.getElementById('selected_slot_id');
        const recommendationError = document.getElementById('recommendation-selection-error');
        const bookingForm = document.getElementById('book-appointment-form');

        function setAssistantMessage(text) {
            assistantMessage.textContent = text;
        }

        function renderRecommendations(items) {
            recommendationList.innerHTML = '';
            selectedSlotInput.value = '';

            if (!items.length) {
                return;
            }

            items.forEach(function (item, index) {
                const card = document.createElement('button');
                card.type = 'button';
                card.className = 'recommendation-chip';
                card.dataset.slotId = String(item.slot_id);
                card.innerHTML =
                    '<strong>Option ' + (index + 1) + '</strong>' +
                    '<span>' + item.display + '</span>' +
                    '<span>' + (item.doctor_name || 'Assigned doctor') + '</span>' +
                    '<small>Remaining capacity: ' + item.remaining_capacity + '</small>';

                card.addEventListener('click', function () {
                    recommendationList.querySelectorAll('.recommendation-chip').forEach(function (el) {
                        el.classList.remove('is-selected');
                    });
                    card.classList.add('is-selected');

                    dateInput.value = item.date;
                    timeInput.value = item.time;
                    if (item.doctor_id) {
                        doctorSelect.value = String(item.doctor_id);
                    }
                    selectedSlotInput.value = String(item.slot_id);
                    if (recommendationError) {
                        recommendationError.hidden = true;
                    }
                    setAssistantMessage('eKesihatan pinned this recommended slot for you. You can submit directly now.');
                });

                recommendationList.appendChild(card);
            });
        }

        async function fetchRecommendations() {
            const serviceId = serviceSelect.value;
            const preferredDate = dateInput.value;

            if (!serviceId || !preferredDate) {
                setAssistantMessage('Pick your service and preferred date first, then eKesihatan can recommend the best slots.');
                renderRecommendations([]);
                return;
            }

            setAssistantMessage('Analyzing best available slots using greedy ranking...');

            const params = new URLSearchParams({
                health_service_id: serviceId,
                preferred_date: preferredDate,
            });

            if (doctorSelect.value) {
                params.append('doctor_id', doctorSelect.value);
            }

            if (timeInput.value) {
                params.append('preferred_time', timeInput.value);
            }

            try {
                const response = await fetch('<?php echo e(route('patient.appointments.recommendations')); ?>?' + params.toString(), {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to get recommendations.');
                }

                const payload = await response.json();
                const recommendations = payload.recommendations || [];
                renderRecommendations(recommendations);

                if (recommendations.length) {
                    setAssistantMessage('Here are your top slots. They are ranked by earliest valid time, capacity usage, and workload balance.');
                } else {
                    setAssistantMessage('eKesihatan could not find valid slots with your current filters. Try another date/time.');
                }
            } catch (error) {
                setAssistantMessage('Recommendation service is temporarily unavailable. You can still submit and auto-allocation will run.');
                renderRecommendations([]);
            }
        }

        serviceSelect.addEventListener('change', function () {
            renderDoctors(this.value);
            fetchRecommendations();
        });

        doctorSelect.addEventListener('change', fetchRecommendations);
        dateInput.addEventListener('change', fetchRecommendations);
        timeInput.addEventListener('change', fetchRecommendations);
        recommendButton.addEventListener('click', fetchRecommendations);

        bookingForm.addEventListener('submit', function (event) {
            if (!selectedSlotInput.value) {
                event.preventDefault();
                if (recommendationError) {
                    recommendationError.hidden = false;
                }
                setAssistantMessage('Please choose one recommended slot before submitting your booking.');
            }
        });

        renderDoctors(serviceSelect.value);
        fetchRecommendations();
    });
</script>
<div class="page-header appointment-page-header">
    <div>
        <h2>Book Appointment</h2>
        <p>Choose your service preferences and let the scheduling assistant lock the best available slot.</p>
    </div>
</div>

<form id="book-appointment-form" method="POST" action="<?php echo e(route('patient.appointments.store')); ?>" class="booking-form-shell">
    <?php echo csrf_field(); ?>
    <div class="booking-layout">
        <div class="booking-column">
            <section class="booking-section">
                <div class="booking-section__head">
                    <h3>Appointment Preferences</h3>
                    <span class="booking-tag">Required</span>
                </div>

                <div class="booking-grid booking-grid--two">
                    <div class="booking-field">
                        <label for="health_service_id">Health Service</label>
                        <select id="health_service_id" name="health_service_id" required>
                            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($service->id); ?>" <?php if((int) $selectedServiceId === (int) $service->id): echo 'selected'; endif; ?>><?php echo e($service->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['health_service_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="assistant-error"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="booking-field booking-field--compact">
                        <label for="preferred_date" data-i18n="Preferred Date">Preferred Date</label>
                        <input id="preferred_date" name="preferred_date" type="date" min="<?php echo e(now()->toDateString()); ?>" max="<?php echo e(now()->addDays(30)->toDateString()); ?>" value="<?php echo e(old('preferred_date')); ?>" required>
                        <?php $__errorArgs = ['preferred_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="assistant-error"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="booking-field booking-field--full">
                        <label for="doctor_id" data-i18n="Preferred Doctor (optional)">Preferred Doctor (optional)</label>
                        <select id="doctor_id" name="doctor_id">
                            <option value="" data-i18n="Any available doctor">Any available doctor</option>
                            <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($doctor->id); ?>"><?php echo e($doctor->name); ?> (<?php echo e($doctor->specialization ?? 'General'); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="booking-field booking-field--compact">
                        <label for="preferred_time">Preferred Time (optional)</label>
                        <input id="preferred_time" name="preferred_time" type="time" value="<?php echo e(old('preferred_time')); ?>">
                    </div>
                </div>
            </section>

            <section class="booking-section">
                <div class="booking-section__head">
                    <h3>Additional Notes</h3>
                    <span class="booking-tag muted">Optional</span>
                </div>
                <div class="booking-field">
                    <label for="notes">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Include any symptoms, timing preference, or important context."><?php echo e(old('notes')); ?></textarea>
                </div>
            </section>

            <div class="booking-submit-row">
                <button type="submit" class="button-link">Submit Booking</button>
            </div>
        </div>

        <section class="booking-section assistant-shell" aria-live="polite">
            <div class="assistant-head">
                <div>
                    <h3>Scheduling Assistant</h3>
                    <p class="assistant-subtitle">Select one recommended slot to continue booking.</p>
                </div>
                <button id="assistant-recommend-btn" type="button">Refresh Slots</button>
            </div>
            <p id="assistant-message">Set your preferred date/time and eKesihatan will suggest the strongest options.</p>
            <div id="assistant-recommendations"></div>
            <input type="hidden" id="selected_slot_id" name="selected_slot_id" value="<?php echo e(old('selected_slot_id')); ?>">
            <p id="recommendation-selection-error" class="assistant-error" hidden>Please choose one recommended option before submitting your booking.</p>
            <?php $__errorArgs = ['selected_slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <p class="assistant-error"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </section>
    </div>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/patient/appointments/create.blade.php ENDPATH**/ ?>