@extends('organization.layouts.app')
@section('organization_content')
<div class="container-fluid">
    <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
        <h2 class="mb-3 me-auto">New Business Admin</h2>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">New Business Admin</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Add New Business Admin Account</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('org.business.admins.store', $business->slug) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Admin Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Admin Email</label>
                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Position</label>
                                <input type="text" name="position" class="form-control" placeholder="Manager / Owner">
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="default-select form-control wide">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="row">
                                @php
                                    $perms = [
                                        'manage_employees' => 'Manage Employees',
                                        'manage_services' => 'Manage Services',
                                        'manage_bookings' => 'Manage Bookings',
                                        'manage_invoices' => 'Manage Invoices',
                                        'view_reports' => 'View Reports',
                                    ];
                                @endphp

                                @foreach($perms as $key => $label)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}"
                                                id="perm_{{ $key }}">
                                            <label class="form-check-label" for="perm_{{ $key }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Business Admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection