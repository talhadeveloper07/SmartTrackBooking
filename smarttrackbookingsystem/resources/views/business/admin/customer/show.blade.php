@extends('business.layouts.app')

@section('business_content')
<div class="container">

    <div class="d-flex align-items-center mb-3">
        <h3 class="me-auto">{{ ucwords($customer->user->name) }}</h3>

        <a href="{{ route('business.customers.edit', [$business->slug, $customer->id]) }}"
           class="btn btn-primary me-2">
            <i class="fa fa-edit"></i> Edit
        </a>

        <a href="{{ route('business.customers.index', $business->slug) }}" class="btn btn-light">Back</a>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Customer Details</strong></div>
        <div class="card-body">

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Customer ID</div>
                    <div class="fw-semibold">{{ $customer->customer_id ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Name</div>
                    <div class="fw-semibold">{{ ucwords($customer->user->name) }}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Status</div>
                    <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Email</div>
                    <div class="fw-semibold">{{ $customer->user->email }}</div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">Phone</div>
                    <div class="fw-semibold">{{ $customer->phone ?? '-' }}</div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <form method="POST" action="{{ route('business.customers.destroy', [$business->slug, $customer->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger btn-delete-customer">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

<script>
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
</script>
@endsection
