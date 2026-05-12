@extends('business.layouts.app')

@section('business_content')

<div class="container-fluid">


    {{-- MAIN CONTENT --}}
    <div class="content">

        <h3 class="mb-4">Subscription Plans</h3>

        {{-- CURRENT PLAN --}}
        @if($subscription)
        <div class="plan-card active-plan">

            <div class="plan-left">
                <h5>{{ $subscription->plan->name ?? 'Premium Plan' }}</h5>
                <h3 class="d-flex align-items-center gap-2">${{ $subscription->plan->price ?? '29.99' }} <span class="badge bg-success">Current Plan</span> </h3>


                <p>Next billing:
                    {{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : 'N/A' }}
                </p>

                <ul class="p-0">
                    <li>✔ {{ $subscription->plan->max_employees }} no of <strong>Employees</strong></li>
                    <li>✔ {{ $subscription->plan->max_employees }} no of <strong>Services</strong></li>
                    <li>✔ {{ $subscription->plan->max_employees }} no of <strong>Bookings</strong></li>
                </ul>

                <div class="alert-box">
                    Your subscription will renew automatically
                </div>
            </div>

            <div class="plan-right d-flex justify-content-start flex-column">
                <a href="{{ route('business.billing.portal', $business->slug) }}" class="btn light text-dark">
                    Manage Billing
                </a>
            </div>

        </div>
        @endif

        {{-- OTHER PLANS --}}
        <div class="plans-grid">

            @foreach($plans as $plan)

                @if(!$subscription || $subscription->plan_id != $plan->id)

                <div class="plan-card">

                    <h5>{{ $plan->name }}</h5>
                    <h3>${{ $plan->price }}/Month</h3>

                    <ul>
                        <li>✔ Feature 1</li>
                        <li>✔ Feature 2</li>
                        <li>✔ Feature 3</li>
                    </ul>

                    <form method="POST" action="{{ route('business.upgrade.plan', $business->slug) }}">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                        <button class="btn primary w-100">
                            Upgrade
                        </button>
                    </form>

                </div>

                @endif

            @endforeach

        </div>

    </div>

</div>

@endsection