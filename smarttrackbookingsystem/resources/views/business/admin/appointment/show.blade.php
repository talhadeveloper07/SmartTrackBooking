@extends('business.layouts.app')

@section('business_content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Appointment Details</h3>
            <div class="text-muted">
                #{{ $appointment->id }} •
                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('business.appointments.index', $business->slug) }}" class="btn btn-light">
                <i class="fa fa-arrow-left me-2"></i> Back
            </a>

            @if(!in_array($appointment->status, ['cancelled','completed'], true))
                <form method="POST"
                      action="{{ route('business.appointments.cancel', [$business->slug, $appointment->id]) }}"
                      onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times me-2"></i> Cancel
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-4">

        {{-- Appointment Summary --}}
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Appointment</h5>
                </div>

                <div class="card-body">
                    @php
                        $date = \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y');
                        $start = \Carbon\Carbon::parse($appointment->start_time)->format('h:i A');
                        $end = \Carbon\Carbon::parse($appointment->end_time)->format('h:i A');

                        $status = $appointment->status;
                        $badge = match ($status) {
                            'confirmed' => 'bg-success',
                            'pending' => 'bg-warning',
                            'completed' => 'bg-primary',
                            'cancelled' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Date</div>
                            <div class="fw-semibold">{{ $date }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Time</div>
                            <div class="fw-semibold">{{ $start }} - {{ $end }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Duration</div>
                            <div class="fw-semibold">{{ $appointment->duration_minutes }} min</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Price</div>
                            <div class="fw-semibold">${{ number_format((float)($appointment->price ?? 0), 2) }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Status</div>
                            <span class="badge {{ $badge }}">{{ ucfirst($status) }}</span>
                        </div>

                        <div class="col-md-12 mb-0">
                            <div class="text-muted">Notes</div>
                            <div class="fw-semibold">
                                {{ $appointment->notes ?: '—' }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Customer --}}
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Customer</h5>
                </div>

                <div class="card-body">
                    @php
                        $customer = $appointment->customer;
                        $user = $customer?->user;
                    @endphp

                    <div class="mb-3">
                        <div class="text-muted">Name</div>
                        <div class="fw-semibold">{{ $user->name ?? $customer->name ?? '—' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted">Email</div>
                        <div class="fw-semibold">{{ $user->email ?? $customer->email ?? '—' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted">Phone</div>
                        <div class="fw-semibold">{{ $customer->phone ?? '—' }}</div>
                    </div>

                    <div class="mb-0">
                        <div class="text-muted">Customer ID</div>
                        <div class="fw-semibold">{{ $customer->customer_id ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service + Employee --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Service & Employee</h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Service</div>
                            <div class="fw-semibold">{{ $appointment->service->name ?? '—' }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Employee</div>
                            <div class="fw-semibold">{{ $appointment->employee->name ?? '—' }}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection