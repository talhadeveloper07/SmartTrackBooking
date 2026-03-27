@extends('layouts.app')

@section('content')

<div class="booking-wrapper">

    {{-- CARD --}}
    <div class="booking-card">

        {{-- STEP PROGRESS --}}
        <div class="steps-progress">
            <div class="step-item active" data-step="0">
                <span>1</span>
                <p>Business</p>
            </div>
            <div class="step-item" data-step="1">
                <span>2</span>
                <p>Address</p>
            </div>
            <div class="step-item" data-step="2">
                <span>3</span>
                <p>Details</p>
            </div>
            <div class="step-item" data-step="3">
                <span>4</span>
                <p>Owner</p>
            </div>
        </div>

        {{-- PLAN --}}
        <div class="plan-box">
            <strong>{{ $plan->name }}</strong>
            <span>${{ $plan->price }}</span>
        </div>

        <form method="POST" action="{{ route('register.business.submit') }}">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $plan->id }}">

            {{-- STEP 1 --}}
            <div class="step step-1">
                <h4>Your Business Info</h4>

                <input type="text" name="business_name" placeholder="Business Name" required>
                <input type="email" name="email" placeholder="Business Email" required>
                <input type="text" name="phone" placeholder="Phone">

                <button type="button" class="next-btn">Continue →</button>
            </div>

            {{-- STEP 2 --}}
            <div class="step step-2 d-none">
                <h4>Address Info</h4>

                <input type="text" name="address" placeholder="Address">
                <input type="text" name="city" placeholder="City">
                <input type="text" name="state" placeholder="State">
                <input type="text" name="country" placeholder="Country">
                <input type="text" name="postal_code" placeholder="Postal Code">

                <div class="btn-group">
                    <button type="button" class="back-btn">← Back</button>
                    <button type="button" class="next-btn">Continue →</button>
                </div>
            </div>

            {{-- STEP 3 --}}
            <div class="step step-3 d-none">
                <h4>Business Details</h4>

                <input type="text" name="business_type" placeholder="Business Type">
                <textarea name="description" placeholder="Description"></textarea>

                <div class="btn-group">
                    <button type="button" class="back-btn">← Back</button>
                    <button type="button" class="next-btn">Continue →</button>
                </div>
            </div>

            {{-- STEP 4 --}}
            <div class="step step-4 d-none">
                <h4>Owner Info</h4>

                <input type="text" name="owner_name" placeholder="Owner Name" required>

                <div class="btn-group">
                    <button type="button" class="back-btn">← Back</button>
                    <button type="submit" id="submitBtn">Continue to Payment →</button>
                </div>
            </div>

        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    let currentStep = 0;
    const steps = document.querySelectorAll('.step');
    const indicators = document.querySelectorAll('.step-item');
    const submitBtn = document.getElementById('submitBtn');

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('d-none', i !== index);
        });

        indicators.forEach((item, i) => {
            item.classList.toggle('active', i <= index);
        });
    }

    function validateStep(step) {
        let inputs = step.querySelectorAll('input[required]');
        for (let input of inputs) {
            if (!input.value.trim()) {
                input.focus();
                alert('Please fill required fields');
                return false;
            }
        }
        return true;
    }

    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!validateStep(steps[currentStep])) return;
            currentStep++;
            showStep(currentStep);
        });
    });

    document.querySelectorAll('.back-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentStep--;
            showStep(currentStep);
        });
    });

    document.querySelector('form').addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Processing... ⏳';
    });

    showStep(currentStep);
});
</script>

@endsection