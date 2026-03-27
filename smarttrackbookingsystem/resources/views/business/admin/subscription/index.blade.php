@extends('business.layouts.app')

@section('business_content')

    <div class="container">

        <h3 class="mb-4">Subscription</h3>

        {{-- CURRENT PLAN --}}
        <div class="card mb-4">
            <div class="card-body">

                <h5>Current Plan</h5>

                @if($subscription)
                    <p><strong>Status:</strong> {{ ucfirst($subscription->status) }}</p>

                    <p>
                        <strong>Ends At:</strong>
                        {{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : 'N/A' }}
                    </p>

                    @if($subscription->status === 'trial')
                        <div class="alert alert-warning">
                            Trial ends in {{ now()->diffInDays($subscription->trial_ends_at) }} days
                        </div>
                    @endif
                @else
                    <p>No active subscription</p>
                @endif

            </div>
        </div>

        {{-- PLANS --}}
        <div class="row">

            @foreach($plans as $plan)

                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body text-center">

                            <h5>{{ $plan->name }}</h5>
                            <h4>${{ $plan->price }}</h4>

                            @if($subscription && $subscription->plan_id == $plan->id)
                                <button class="btn btn-secondary" disabled>
                                    Current Plan
                                </button>
                            @else
                                <form method="POST" action="{{ route('business.upgrade.plan', $business->slug) }}">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">

                                    <button class="btn btn-success">Upgrade</button>
                                </form>
                            @endif

                        </div>
                    </div>
                </div>

            @endforeach

        </div>

    </div>

@endsection