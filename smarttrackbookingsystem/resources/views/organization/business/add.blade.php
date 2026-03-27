@extends('organization.layouts.app')
@section('organization_content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">New Business Account</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">New Business Account</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add New Business</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('org.store.business') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Business Name</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter business name" required>
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Business Type</label>
                                        <input type="text" name="business_type" class="form-control"
                                            placeholder="Salon / Gym / Clinic">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="Business Email">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-control" placeholder="Phone Number">
                                    </div>

                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Address</label>
                                        <input type="text" name="address" class="form-control"
                                            placeholder="123 Street Name">
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control">
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" class="form-control">
                                    </div>

                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Postal Code</label>
                                        <input type="text" name="postal_code" class="form-control">
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Country</label>
                                        <input type="text" name="country" class="form-control">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="default-select form-control wide">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Business Logo</label>
                                        <input type="file" name="logo" class="form-control">
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Cover Image</label>
                                        <input type="file" name="cover_image" class="form-control">
                                    </div>

                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" rows="4" class="form-control"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Subscription Plan <span class="text-danger">*</span></label>
                                    <select name="plan_id" class="default-select form-control wide" required>
                                        <option value="">Select a Plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">
                                                {{ $plan->name }} - (${{ $plan->price }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Business Hours (JSON or text)</label>
                                    <textarea name="business_hours" rows="3" class="form-control"
                                        placeholder='Mon-Fri 9AM-6PM'></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Create Business</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection