@extends('business.layouts.app')

@section('business_content')
@php
    $days = [
        1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
        5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'
    ];
@endphp

<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="m-0">{{ ucwords($employee->name) }}</h3>
            <p class="m-0">{{ $employee->employee_id }}</p>
        </div>
        <div>
            <a href="{{ route('business.employees.edit', [$business->slug, $employee->id]) }}"
               class="btn btn-primary me-2">Edit</a>

            <a href="{{ route('business.employees', $business->slug) }}" class="btn btn-light">Back</a>
        </div>
    </div>

    {{-- Profile header --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="profile card card-body px-3 pt-3 pb-0">
                <div class="profile-head">
                    <div class="photo-content">
                        <div class="cover-photo rounded"></div>
                    </div>

                    <div class="profile-info">
                        <div class="profile-photo">
                            <img src="images/profile/profile.png" class="img-fluid rounded-circle" alt="">
                        </div>

                        <div class="profile-details">
                            <div class="profile-name px-3 pt-2">
                                <h4 class="text-primary mb-0">{{ ucwords($employee->name) }}</h4>
                                <p>{{ $employee->employee_id }}</p>
                            </div>

                            <div class="profile-email px-2 pt-2">
                                <h4 class="text-muted mb-0">
                                    <i class="fa fa-envelope"></i> {{ $employee->email }}
                                </h4>
                                <p class="m-0"><i class="fa fa-phone"></i> {{ $employee->phone }}</p>
                            </div>

                            <div class="dropdown ms-auto">
                                <span class="badge bg-success">{{ $employee->status }}</span>

                                <a href="#" class="btn btn-primary p-1 light" data-bs-toggle="dropdown" aria-expanded="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 24 24">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"></rect>
                                            <circle fill="#000000" cx="5" cy="12" r="2"></circle>
                                            <circle fill="#000000" cx="12" cy="12" r="2"></circle>
                                            <circle fill="#000000" cx="19" cy="12" r="2"></circle>
                                        </g>
                                    </svg>
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li class="dropdown-item"><i class="fa fa-user-circle text-primary me-2"></i> View profile</li>
                                    <li class="dropdown-item"><i class="fa fa-users text-primary me-2"></i> Add to friends</li>
                                    <li class="dropdown-item"><i class="fa fa-plus text-primary me-2"></i> Add to group</li>
                                    <li class="dropdown-item"><i class="fa fa-ban text-primary me-2"></i> Block</li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main row --}}
    <div class="row">
        {{-- Left side --}}
        <div class="col-xl-4">
            <div class="row">

                {{-- Stats --}}
                <div class="col-xl-12">
                    <div class="card h-auto">
                        <div class="card-body">
                            <div class="profile-statistics">
                                <div class="text-center">
                                    <div class="row text-center">
                                        <div class="col">
                                            <h3 class="m-b-0">{{ $completedAppointments ?? 0 }}</h3>
                                            <span>Completed</span>
                                        </div>
                                        <div class="col">
                                            <h3 class="m-b-0">{{ $inProgressAppointments ?? 0 }}</h3>
                                            <span>In Progress</span>
                                        </div>
                                        <div class="col">
                                            <h3 class="m-b-0">{{ $upcomingAppointments ?? 0 }}</h3>
                                            <span>Upcoming</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Upcoming Appointments --}}
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">Upcoming Appointments</h4>
                            <span class="text-muted">{{ $upcomingCount ?? 0 }} total</span>
                        </div>

                        <div class="card-body">
                            @if(($upcomingItems?->count() ?? 0) === 0)
                                <div class="text-muted">No upcoming appointments.</div>
                            @else
                                <div class="d-flex flex-column gap-2">
                                    @foreach($upcomingItems as $item)
                                        @php
                                            $appt = $item->appointment;
                                            $customer = $appt?->customer;
                                            $customerName = $customer?->user?->name ?? '—';

                                            $date = $item->appointment_date
                                                ? \Carbon\Carbon::parse($item->appointment_date)->format('d M')
                                                : '—';

                                            $start = $item->start_time
                                                ? \Carbon\Carbon::parse($item->start_time)->format('h:i A')
                                                : '—';

                                            $end = $item->end_time
                                                ? \Carbon\Carbon::parse($item->end_time)->format('h:i A')
                                                : '—';
                                        @endphp

                                        <a class="text-decoration-none"
                                           href="{{ route('business.appointments.show', [$business->slug, $appt->id]) }}">
                                            <div class="border rounded p-3">
                                                <div class="d-flex align-items-start justify-content-between">
                                                    <div>
                                                        <div class="fw-bold text-dark">{{ $customerName }}</div>
                                                        <div class="text-muted small">{{ $date }}</div>
                                                    </div>

                                                    <div class="text-end text-dark">
                                                        <div class="fw-semibold">{{ $start }} - {{ $end }}</div>
                                                        <div class="text-muted small">{{ (int)($item->duration_minutes ?? 0) }} mins</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>

                                <div class="text-center mt-3">
                                    <a href="{{ route('business.appointments.index', $business->slug) }}"
                                       class="btn btn-primary btn-sm">
                                        View All Appointments
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Right side --}}
        <div class="col-xl-8">
            <div class="card h-auto">
                <div class="card-body">
                    <div class="profile-tab">
                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a href="#my-posts" data-bs-toggle="tab" class="nav-link active show">
                                        Working Schedule
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#assigned-services" data-bs-toggle="tab" class="nav-link">
                                        Assigned Services
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                {{-- Working schedule --}}
                                <div id="my-posts" class="tab-pane fade active show">
                                    <div class="my-post-content pt-3">
                                        @foreach($days as $dayIndex => $dayName)
                                            @php
                                                $rows = $schedule[$dayIndex] ?? collect();

                                                // if no record => off
                                                $isOff = $rows->count() ? (bool)($rows->first()->is_off) : true;

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
                                                                        {{ \Carbon\Carbon::parse($s->start_time)->format('h:i A') }}
                                                                        –
                                                                        {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }}
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

                                {{-- Assigned services --}}
                                <div id="assigned-services" class="tab-pane">
                                    <div class="my-post-content pt-3">
                                        @if($employee->services->count())
                                            <div>
                                                @foreach($employee->services as $srv)
                                                    <p class="mb-2">{{ ucwords($srv->name) }}</p>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted">No services assigned.</div>
                                        @endif
                                    </div>
                                </div>

                            </div> {{-- tab-content --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- row --}}

</div>
@endsection