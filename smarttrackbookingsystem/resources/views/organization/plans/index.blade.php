@extends('organization.layouts.app')

@section('organization_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Subscription Plans</h3>
        <a href="{{ route('org.plans.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Create New Plan
        </a>
    </div>

    <div class="row">
        @foreach($plans as $plan)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4 text-center">
                    <h4 class="card-title text-primary fw-bold">{{ $plan->name }}</h4>
                    <h2 class="mt-3 mb-0">${{ number_format($plan->price, 2) }}</h2>
                    <p class="text-muted">per month</p>
                </div>
                
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span><i class="fas fa-users text-success me-2"></i> Max Employees</span>
                            <span class="fw-bold">{{ $plan->max_employees }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span><i class="fas fa-concierge-bell text-success me-2"></i> Max Services</span>
                            <span class="fw-bold">{{ $plan->max_services }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <span><i class="fas fa-calendar-check text-success me-2"></i> Max Bookings</span>
                            <span class="fw-bold">{{ $plan->max_bookings }}</span>
                        </li>
                    </ul>

                    <div class="alert alert-info py-2 text-center mb-0">
                        <strong>{{ $plan->subscriptions_count }}</strong> Active Businesses
                    </div>
                </div>

                <div class="card-footer bg-light border-0 d-flex justify-content-center gap-2 pb-4">
                    <a href="{{ route('org.plans.edit', $plan->id) }}" class="btn btn-warning px-4">
                        Edit
                    </a>

                    <form method="POST" action="{{ route('org.plans.destroy', $plan->id) }}" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection