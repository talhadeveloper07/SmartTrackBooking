@extends('business.layouts.app')

@section('business_content')
<div class="container">

    <div class="d-flex align-items-center mb-3">
        <h3 class="me-auto">Edit Customer — {{ ucwords($customer->user->name) }}</h3>
        <a href="{{ route('business.customers.show', [$business->slug, $customer->id]) }}" class="btn btn-light">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('business.customers.update', [$business->slug, $customer->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Customer ID (optional)</label>
                        <input type="text" name="customer_id" class="form-control"
                               value="{{ old('customer_id', $customer->customer_id) }}" placeholder="CUS-001" readonly>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $customer->user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $customer->user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $customer->phone) }}">
                    </div>

                   

                    <div class="mb-3 col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="default-select form-control wide">
                            <option value="active" {{ old('status',$customer->status)=='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status',$customer->status)=='inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary">Update Customer</button>
                <a href="{{ route('business.customers.show', [$business->slug, $customer->id]) }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection