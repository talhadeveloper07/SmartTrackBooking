@extends('organization.layouts.app')

@section('organization_content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Edit Business Account</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Business Account</a></li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3"><h3 class="m-0">{{ucfirst($business->name)}}</h3></div>
            <div class="card-body">
                <form action="{{ route('org.business.update', $business->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $business->name) }}"
                                required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Business Type</label>
                            <input type="text" name="business_type" class="form-control"
                                value="{{ old('business_type', $business->business_type) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ old('email', $business->email) }}">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $business->phone) }}">
                        </div>

                        <div class="mb-3 col-md-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control"
                                value="{{ old('address', $business->address) }}">
                        </div>

                    </div>

                    <div class="row">

                        <div class="mb-3 col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $business->city) }}">
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control"
                                value="{{ old('state', $business->state) }}">
                        </div>

                        <div class="mb-3 col-md-4">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control"
                                value="{{ old('postal_code', $business->postal_code) }}">
                        </div>

                    </div>

                    <div class="row">

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control"
                                value="{{ old('country', $business->country) }}">
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="default-select form-control wide">
                                <option value="active" {{ old('status', $business->status) == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive" {{ old('status', $business->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                    </div>

                    <div class="row">

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Business Logo</label>
                            <input type="file" name="logo" class="form-control">
                            @if($business->logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $business->logo) }}" width="60" alt="logo">
                                </div>
                            @endif
                            @error('logo') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Cover Image</label>
                            <input type="file" name="cover_image" class="form-control">
                            @if($business->cover_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $business->cover_image) }}" width="100" alt="cover">
                                </div>
                            @endif
                            @error('cover_image') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4"
                            class="form-control">{{ old('description', $business->description) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Business Hours</label>
                        <textarea name="business_hours" rows="3"
                            class="form-control">{{ old('business_hours', $business->business_hours) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Business</button>
                    <a href="{{ route('org.business-accounts') }}" class="btn btn-light">Cancel</a>

                </form>
            </div>
        </div>
    </div>
@endsection