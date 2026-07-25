<?php $__env->startSection('content'); ?>
<h2>Reschedule Appointment</h2>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const doctorSelect = document.getElementById('doctor_id');
        const dateInput = document.getElementById('preferred_date');
        const timeInput = document.getElementById('preferred_time');
        const assistantMessage = document.getElementById('assistant-message');
        const recommendationList = document.getElementById('assistant-recommendations');
        const recommendButton = document.getElementById('assistant-recommend-btn');
        const serviceId = '<?php echo e($appointment->health_service_id); ?>';

        function setAssistantMessage(text) {
            assistantMessage.textContent = text;
        }

        function renderRecommendations(items) {
            recommendationList.innerHTML = '';

            if (!items.length) {
                const empty = document.createElement('p');
                empty.textContent = 'No recommendation yet. Try another date or time.';
                recommendationList.appendChild(empty);
                return;
            }

            items.forEach(function (item, index) {
                const card = document.createElement('button');
                card.type = 'button';
                card.className = 'recommendation-chip';
                card.innerHTML =
                    '<strong>Option ' + (index + 1) + '</strong>' +
                    '<span>' + item.display + '</span>' +
                    '<span>' + (item.doctor_name || 'Assigned doctor') + '</span>' +
                    '<small>Remaining capacity: ' + item.remaining_capacity + '</small>';

                card.addEventListener('click', function () {
                    dateInput.value = item.date;
                    timeInput.value = item.time;
                    if (item.doctor_id) {
                        doctorSelect.value = String(item.doctor_id);
                    }
                    setAssistantMessage('Applied. This recommendation is now selected for your reschedule request.');
                });

                recommendationList.appendChild(card);
            });
        }

        async function fetchRecommendations() {
            if (!serviceId || !dateInput.value) {
                renderRecommendations([]);
                return;
            }

            setAssistantMessage('Checking greedy recommendations...');

            const params = new URLSearchParams({
                health_service_id: serviceId,
                preferred_date: dateInput.value,
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
                    throw new Error('Could not load recommendations.');
                }

                const payload = await response.json();
                const recommendations = payload.recommendations || [];
                renderRecommendations(recommendations);
                setAssistantMessage(recommendations.length
                    ? 'Top alternatives found. Pick one to auto-fill your preferred date and time.'
                    : 'No valid alternatives found for this preference.');
            } catch (error) {
                setAssistantMessage('Recommendation service is unavailable right now. You can still submit manually.');
                renderRecommendations([]);
            }
        }

        doctorSelect.addEventListener('change', fetchRecommendations);
        dateInput.addEventListener('change', fetchRecommendations);
        timeInput.addEventListener('change', fetchRecommendations);
        recommendButton.addEventListener('click', fetchRecommendations);

        fetchRecommendations();
    });
</script>

<style>
    .assistant-shell {
        border: 1px solid #dbe3f0;
        background: linear-gradient(135deg, #eef6ff 0%, #f8f5ff 100%);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .assistant-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.6rem;
    }

    .assistant-head h3 {
        margin: 0;
    }

    #assistant-message {
        margin: 0.35rem 0 0.75rem;
    }

    #assistant-recommendations {
        display: grid;
        gap: 0.55rem;
    }

    .recommendation-chip {
        width: 100%;
        text-align: left;
        display: grid;
        gap: 0.2rem;
        padding: 0.65rem 0.75rem;
        border: 1px solid #cad6ee;
        border-radius: 0.75rem;
        background: #ffffff;
        color: #1f2937;
        cursor: pointer;
    }

    .recommendation-chip strong,
    .recommendation-chip span,
    .recommendation-chip small {
        color: #1f2937;
    }

    .recommendation-chip:hover {
        border-color: #5b83d8;
        background: #f4f8ff;
    }
</style>

<section class="assistant-shell" aria-live="polite">
    <div class="assistant-head">
        <h3>Reschedule Assistant</h3>
        <button id="assistant-recommend-btn" type="button">Recommend Alternatives</button>
    </div>
    <p id="assistant-message">eKesihatan can suggest the best next slots before you submit.</p>
    <div id="assistant-recommendations"></div>
</section>

<form method="POST" action="<?php echo e(route('patient.appointments.update', $appointment)); ?>">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div>
        <label for="doctor_id" data-i18n="Preferred Doctor (optional)">Preferred Doctor (optional)</label>
        <select id="doctor_id" name="doctor_id">
            <option value="" data-i18n="Any available doctor">Any available doctor</option>
            <?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($doctor->id); ?>" <?php if($appointment->doctor_id === $doctor->id): echo 'selected'; endif; ?>><?php echo e($doctor->name); ?> (<?php echo e($doctor->specialization ?? 'General'); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div>
        <label for="preferred_date" data-i18n="Preferred Date">Preferred Date</label>
        <input id="preferred_date" name="preferred_date" type="date" min="<?php echo e(now()->toDateString()); ?>" max="<?php echo e(now()->addDays(30)->toDateString()); ?>" value="<?php echo e(old('preferred_date', $appointment->scheduled_at->format('Y-m-d'))); ?>" required>
    </div>
    <div>
        <label for="preferred_time">Preferred Time (optional)</label>
        <input id="preferred_time" name="preferred_time" type="time" value="<?php echo e(old('preferred_time', $appointment->scheduled_at->format('H:i'))); ?>">
    </div>
    <button type="submit">Update Appointment</button>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/ekesihat/ekesihatan/resources/views/patient/appointments/edit.blade.php ENDPATH**/ ?>