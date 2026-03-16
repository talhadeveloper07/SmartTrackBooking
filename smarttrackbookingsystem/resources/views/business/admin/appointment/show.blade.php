@extends('business.layouts.app')

@section('business_content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Appointment Details</h3>
            <div class="text-muted">
                #{{ $appointment->id }}
                @if(($appointment->items?->count() ?? 0) > 0)
                    • {{ \Carbon\Carbon::parse($appointment->items->min('appointment_date'))->format('d M Y') }}
                    @if($appointment->items->max('appointment_date') && $appointment->items->max('appointment_date') !== $appointment->items->min('appointment_date'))
                        - {{ \Carbon\Carbon::parse($appointment->items->max('appointment_date'))->format('d M Y') }}
                    @endif
                @else
                    • {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('business.appointments.index', $business->slug) }}" class="btn btn-light">
                <i class="fa fa-arrow-left me-2"></i> Back
            </a>

            {{-- Cancel whole appointment (optional) --}}
            @if(!in_array($appointment->status, ['cancelled','completed'], true))
                <form method="POST"
                      action="{{ route('business.appointments.cancel', [$business->slug, $appointment->id]) }}"
                      onsubmit="return confirm('Are you sure you want to cancel this whole appointment?');">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times me-2"></i> Cancel Booking
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

    @php
        $items = $appointment->items ?? collect();

        $totalItems = $items->count();

        // totals based on items (recommended for multi-service)
        $totalDuration = (int) $items->sum('duration_minutes');
        $totalPrice    = (float) $items->sum('price');

        // overall status on parent (still shown)
        $status = $appointment->status;

        $badge = match ($status) {
            'confirmed' => 'bg-success',
            'pending' => 'bg-warning text-dark',
            'completed' => 'bg-primary',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };

        // Overall earliest/last timing (best effort)
        $minDate = $items->min('appointment_date') ?: $appointment->appointment_date;
        $maxDate = $items->max('appointment_date') ?: $appointment->appointment_date;

        // If you store start_time/end_time on items, show a "first-start to last-end" (not perfect across days)
        $firstStart = $items->sortBy(['appointment_date','start_time'])->first()?->start_time;
        $lastEnd    = $items->sortByDesc(['appointment_date','end_time'])->first()?->end_time;

        $dateLabel = $minDate ? \Carbon\Carbon::parse($minDate)->format('d M Y') : '—';
        if ($maxDate && $maxDate !== $minDate) {
            $dateLabel .= ' - ' . \Carbon\Carbon::parse($maxDate)->format('d M Y');
        }

        $timeLabel = '—';
        if ($firstStart && $lastEnd && $minDate === $maxDate) {
            $timeLabel = \Carbon\Carbon::parse($firstStart)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($lastEnd)->format('h:i A');
        }
    @endphp

    <div class="row g-4">

        {{-- Appointment Summary --}}
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Appointment (Booking Group)</h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Date</div>
                            <div class="fw-semibold">{{ $dateLabel }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Time</div>
                            <div class="fw-semibold">{{ $timeLabel }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Total Duration</div>
                            <div class="fw-semibold">{{ $totalDuration }} min</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Total Price</div>
                            <div class="fw-semibold">${{ number_format($totalPrice, 2) }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Services Count</div>
                            <div class="fw-semibold">{{ $totalItems }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="text-muted">Booking Status</div>
                            <span class="badge {{ $badge }}">{{ ucfirst($status) }}</span>
                        </div>

                        <div class="col-md-12 mb-0">
                            <div class="text-muted">Notes</div>
                            <div class="fw-semibold">{{ $appointment->notes ?: '—' }}</div>
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
                        <div class="fw-semibold">{{ $user->name ?? '—' }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted">Email</div>
                        <div class="fw-semibold">{{ $user->email ?? '—' }}</div>
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

        {{-- MULTI Service Items --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Services</h5>
                    <span class="text-muted small">{{ $totalItems }} item(s)</span>
                </div>

                <div class="card-body">
                    @if($totalItems === 0)
                        <div class="text-muted">No service items found.</div>
                    @else
                        <div class="row g-3">
                            @foreach($appointment->items as $i => $item)
                                @php
                                    $itemDate = $item->appointment_date
                                        ? \Carbon\Carbon::parse($item->appointment_date)->format('d M Y')
                                        : '—';

                                    $itemStart = $item->start_time
                                        ? \Carbon\Carbon::parse($item->start_time)->format('h:i A')
                                        : '—';

                                    $itemEnd = $item->end_time
                                        ? \Carbon\Carbon::parse($item->end_time)->format('h:i A')
                                        : '—';

                                    $duration = (int) ($item->duration_minutes ?? 0);
                                    $price = (float) ($item->price ?? 0);

                                    $itemStatus = $item->status ?? 'confirmed';
                                    $itemBadge = match ($itemStatus) {
                                        'confirmed' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'completed' => 'bg-primary',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp

                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="card h-100 shadow-sm border">
                                        <div class="card-body">

                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                <div class="fw-bold">
                                                    Service #{{ $i + 1 }}
                                                </div>

                                                <div class="text-end">
                                                    <span class="badge {{ $itemBadge }}">{{ ucfirst($itemStatus) }}</span>
                                                    <div class="small text-muted mt-1">{{ $duration }} min</div>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted small">Service</div>
                                                <div class="fw-semibold">{{ $item->service->name ?? '—' }}</div>
                                            </div>

                                            <div class="mb-2">
                                                <div class="text-muted small">Employee</div>
                                                <div class="fw-semibold">{{ $item->employee->name ?? '—' }}</div>
                                            </div>

                                            <div class="row g-2 mb-2">
                                                <div class="col-6">
                                                    <div class="text-muted small">Date</div>
                                                    <div class="fw-semibold">{{ $itemDate }}</div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-muted small">Time</div>
                                                    <div class="fw-semibold">{{ $itemStart }} - {{ $itemEnd }}</div>
                                                </div>
                                                 <div class="col-12">
                                                    <div class="text-muted small">Location</div>
                                                    <div class="fw-semibold">{{ $item->location ?? '-' }}</div>
                                                </div>
                                            </div>

                                            <hr class="my-3">

                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="text-muted">Price</div>
                                                <div class="fw-bold">${{ number_format($price, 2) }}</div>
                                            </div>

                                            {{-- OPTIONAL: Item Actions --}}
                                            <div class="d-flex gap-2 mt-3">
                                                @if(!in_array($itemStatus, ['cancelled','completed'], true))
                                                    <form method="POST"
                                                          action="{{ route('business.appointment-items.complete', [$business->slug, $appointment->id, $item->id]) }}"
                                                          class="w-100"
                                                          onsubmit="return confirm('Mark this service as completed?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                                            Complete
                                                        </button>
                                                    </form>

                                                    <form method="POST"
                                                          action="{{ route('business.appointment-items.cancel', [$business->slug, $appointment->id, $item->id]) }}"
                                                          class="w-100"
                                                          onsubmit="return confirm('Cancel this service item?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                            Cancel
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-light w-100" disabled>
                                                        No actions
                                                    </button>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection