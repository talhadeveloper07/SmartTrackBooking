@extends('business.layouts.app')

@section('business_content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Appointments</h3>

        <a href="{{ route('business.appointments.create', $business->slug) }}" class="btn btn-primary">
            <i class="fa fa-plus me-2"></i> New Appointment
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" id="date_from" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" id="date_to" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Employee</label>
                    <select id="employee_id" class="form-select">
                        <option value="">All</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Service</label>
                    <select id="service_id" class="form-select">
                        <option value="">All</option>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select id="status" class="form-select">
                        <option value="">All</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button id="applyFilters" class="btn btn-dark w-50">Apply</button>
                    <button id="resetFilters" class="btn btn-light w-50">Reset</button>
                </div>

            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="appointmentsTable" class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Employee</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function () {

    const ajaxUrl = "{{ route('business.appointments.data', $business->slug) }}";

    const table = $('#appointmentsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        searching: false, // we use custom filters

        ajax: {
            url: ajaxUrl,
            type: "GET",
            data: function (d) {
                d.status      = $('#status').val();
                d.employee_id = $('#employee_id').val();
                d.service_id  = $('#service_id').val();
                d.date_from   = $('#date_from').val();
                d.date_to     = $('#date_to').val();
            }
        },

        order: [[1, 'desc']],

        columns: [
            { data: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'date', name:'appointment_date' },
            { data: 'time', orderable:false, searchable:false },
            { data: 'customer', orderable:false },
            { data: 'service', orderable:false },
            { data: 'employee', orderable:false },
            { data: 'price', searchable:false },
            { data: 'status_badge', orderable:false, searchable:false },
            { data: 'actions', orderable:false, searchable:false }
        ]
    });

    // APPLY FILTER BUTTON
    $('#applyFilters').click(function (e) {
        e.preventDefault();
        table.draw();
    });

    // RESET FILTERS
    $('#resetFilters').click(function (e) {
        e.preventDefault();

        $('#status').val('');
        $('#employee_id').val('');
        $('#service_id').val('');
        $('#date_from').val('');
        $('#date_to').val('');

        table.draw();
    });

});
</script>
@endsection