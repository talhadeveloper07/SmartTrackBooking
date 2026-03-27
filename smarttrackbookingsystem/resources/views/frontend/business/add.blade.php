@extends('layouts.app')

@section('content')

<div class="container mt-5">

<div class="alert alert-info">
    <strong>Selected Plan:</strong> {{ $plan->name }} <br>
    <strong>Price:</strong> ${{ $plan->price }}
</div>

    <form method="POST" action="{{ route('register.business.submit') }}">
        @csrf

        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

        {{-- STEP 1 --}}
        <div class="step step-1">
            <h4>Step 1: Business Info</h4>

            <input type="text" name="business_name" class="form-control mb-2" placeholder="Business Name" required>
            <input type="email" name="email" class="form-control mb-2" placeholder="Business Email" required>
            <input type="text" name="phone" class="form-control mb-2" placeholder="Phone">

            <button type="button" class="btn btn-primary next-btn">Next</button>
        </div>

        {{-- STEP 2 --}}
        <div class="step step-2 d-none">
            <h4>Step 2: Address</h4>

            <input type="text" name="address" class="form-control mb-2" placeholder="Address">
            <input type="text" name="city" class="form-control mb-2" placeholder="City">
            <input type="text" name="state" class="form-control mb-2" placeholder="State">
            <input type="text" name="country" class="form-control mb-2" placeholder="Country">
            <input type="text" name="postal_code" class="form-control mb-2" placeholder="Postal Code">

            <button type="button" class="btn btn-secondary back-btn">Back</button>
            <button type="button" class="btn btn-primary next-btn">Next</button>
        </div>

        {{-- STEP 3 --}}
        <div class="step step-3 d-none">
            <h4>Step 3: Business Details</h4>

            <input type="text" name="business_type" class="form-control mb-2" placeholder="Business Type">
            <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>

            <button type="button" class="btn btn-secondary back-btn">Back</button>
            <button type="button" class="btn btn-primary next-btn">Next</button>
        </div>

        {{-- STEP 4 --}}
        <div class="step step-4 d-none">
            <h4>Step 4: Owner Info</h4>

            <input type="text" name="owner_name" class="form-control mb-2" placeholder="Owner Name" required>

            <button type="button" class="btn btn-secondary back-btn">Back</button>
            <button type="submit" id="submitBtn" class="btn btn-success">
                Continue to Payment
            </button>
        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let currentStep = 0;
    const steps = document.querySelectorAll('.step');
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('d-none', i !== index);
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

    // ✅ Prevent double submit + show loading
    form.addEventListener('submit', function () {

        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Processing Payment... ⏳';

    });

    showStep(currentStep);
});
</script>

@endsection