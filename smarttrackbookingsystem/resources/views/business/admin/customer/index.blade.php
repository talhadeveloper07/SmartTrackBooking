@extends('business.layouts.app')

@section('business_content')
<div class="container">

    <div class="d-flex align-items-center mb-3">
        <h3 class="me-auto">Customers — {{ ucwords($business->name) }}</h3>
        <a href="{{ route('business.customers.create', $business->slug) }}" class="btn btn-primary">
            <i class="fa fa-plus me-1"></i> Add Customer
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="statusFilter" class="default-select form-control wide">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="resetFilters" class="btn btn-light w-100">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table w-100" id="customersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th width="160">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<script>
$(function () {

    const table = $('#customersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('business.customers.dt', $business->slug) }}",
            data: function (d) {
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'customer_id', name: 'customer_id', defaultContent: '-' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'phone', name: 'phone', defaultContent: '-' },
            { data: 'status', name: 'status',
              render: function(data){
                if(data === 'active') return `<span class="badge bg-success">Active</span>`;
                return `<span class="badge bg-secondary">Inactive</span>`;
              }
            },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    });

    $('#statusFilter').on('change', function(){
        table.ajax.reload();
    });

    $('#resetFilters').on('click', function(){
        $('#statusFilter').val('');
        table.ajax.reload();
    });

    // Delete with SweetAlert
    $(document).on('click', '.btn-delete-customer', function(e){
        e.preventDefault();

        const form = $(this).closest('form');

        Swal.fire({
            icon: 'warning',
            title: 'Delete Customer?',
            text: "This will delete customer's user account as well.",
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if(result.isConfirmed) form.submit();
        });
    });

});
</script>
@endsection
