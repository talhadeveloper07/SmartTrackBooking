@extends('business.layouts.app')

@section('business_content')
@php
    $days = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
        5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'
    ];

    // helper: format 24h to 12h
    $fmt = function($t){
        if(!$t) return '';
        try {
            return \Carbon\Carbon::createFromFormat('H:i', $t)->format('h:ia');
        } catch (\Exception $e){
            return $t;
        }
    };
@endphp

<div class="container">

    <div class="d-flex align-items-center mb-3">
        <h3 class="me-auto">{{ ucwords($employee->name) }}</h3>
        <div>
             <a href="{{ route('business.employees.edit', [$business->slug, $employee->id]) }}" class="btn btn-primary me-2">
       !@
             <a href="{{ route('business.employees', $business->slug) }}" class="btn btn-light">
        Back
    </a>
        </div>
    </div>

    {{-- =================== DETAILS =================== --}}
    <div class="card mb-4">
        <div class="card-header"><strong>Employee Details</strong></div>
        <div class="card-body">

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Employee ID</div>
                    <div class="fw-semibold">{{ $employee->employee_id ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Name</div>
                    <div class="fw-semibold">{{ ucwords($employee->name) }}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Status</div>
                    <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Email</div>
                    <div class="fw-semibold">{{ $employee->email ?? ($employee->user->email ?? '-') }}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Phone</div>
                    <div class="fw-semibold">{{ $employee->phone ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Joining Date</div>
                    <div class="fw-semibold">{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y') : '-' }}</div>
                </div>

                <div class="col-md-12">
                    <div class="text-muted small">Address</div>
                    <div class="fw-semibold">{{ $employee->address ?? '-' }}</div>
                </div>
            </div>

        </div>
    </div>

    {{-- =================== SERVICES =================== --}}
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <strong>Services Offered</strong>
            <span class="text-muted small">{{ $employee->services->count() }} assigned</span>
        </div>

        <div class="card-body">
            @if($employee->services->count())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($employee->services as $srv)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                            {{ ucwords($srv->name) }}
                        </span>
                    @endforeach
                </div>
            @else
                <div class="text-muted">No services assigned.</div>
            @endif
        </div>
    </div>

    {{-- =================== SCHEDULE =================== --}}
    <div class="card mb-4">
        <div class="card-header"><strong>Working Schedule</strong></div>

        <div class="card-body p-0">
            @foreach($days as $dayIndex => $dayName)
                @php
                    $rows = $schedule[$dayIndex] ?? collect();

                    // off if any record says is_off=1 OR no slots and day not present
                    $isOff = $rows->count() ? (bool)($rows->first()->is_off) : true;

                    // if present but not off => show all slots
                    $slots = $rows->where('is_off', false)->values();
                @endphp

                <div class="border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                    <div class="fw-semibold {{ $isOff ? 'text-danger text-decoration-line-through' : '' }}">
                        {{ $dayName }}
                    </div>

                    <div class="text-muted">
                        @if($isOff)
                            <span class="badge bg-light text-muted border">Off</span>
                        @else
                            @if($slots->count())
                                <div class="d-flex flex-column align-items-end gap-1">
                                    @foreach($slots as $s)
                                        <div class="small fw-semibold">
                                            {{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge bg-light text-muted border">No time set</span>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection