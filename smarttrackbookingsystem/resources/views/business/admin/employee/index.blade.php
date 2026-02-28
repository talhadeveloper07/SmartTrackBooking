@extends('business.layouts.app')

@section('business_content')
    <div class="container-fluid">

        <div class="d-flex align-items-center mb-3">
            @if(session('success'))
                <script>
                    toastr.success("{{ session('success') }}");
                </script>
            @endif

            @if(session('error'))
                <script>
                    toastr.error("{{ session('error') }}");
                </script>
            @endif

            <h3 class="me-auto">Employees</h3>

            <a href="{{ route('business.add.service', $business->slug) }}" class="btn btn-primary">
                Add Employee
            </a>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table" id="employeeTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Emp ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Joining Date</th>
                                    <th>Status</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>



    </div>

<script>
$(function () {

    $('#employeeTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('business.employees.data', $business->slug) }}",

        columns: [
            { data: 'DT_RowIndex', orderable:false, searchable:false },
            { data: 'employee_id', name: 'employee_id' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone' },
            { data: 'joining_date', name: 'joining_date' },
            { data: 'status', orderable:false, searchable:false },
            { data: 'action', orderable:false, searchable:false },
        ]
    });

});
</script>

@endsection