@extends('organization.layouts.app')
@section('organization_content')

<div class="container">

    <h3 class="mb-4">{{ ucwords($business->name) }} Details</h3>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">

                <div class="col-md-2">
                    @if($business->logo)
                        <img src="{{ asset('storage/'.$business->logo) }}" class="img-fluid rounded">
                    @endif
                </div>

                <div class="col-md-10">
                    <div class="d-flex justify-content-between">
                        <h4>{{ ucwords($business->name) }}</h4>
                        <a href="{{ route('org.business.edit',$business->slug) }}" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fa fa-pencil"></i></a>
                    </div>
                    <p class="text-muted">{{ $business->business_type }}</p>

                    <div class="row mt-3">
                        <div class="col-md-4"><strong>Email:</strong> {{ $business->email }}</div>
                        <div class="col-md-4"><strong>Phone:</strong> {{ $business->phone }}</div>
                        <div class="col-md-4"><strong>Status:</strong> 
                            <span class="badge bg-success">{{ ucfirst($business->status) }}</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <strong>Address:</strong>
                        {{ $business->address }}, {{ $business->city }}, {{ $business->country }}
                    </div>

                </div>

            </div>
        </div>
    </div>
        <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Business Admins</h4>

            <a href="{{ route('org.business.admins.create',$business->slug) }}"
               class="btn btn-primary btn-sm">
                Add Admin
            </a>
        </div>

        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($business->admins as $admin)
                    <tr>
                        <td>{{ ucwords($admin->name) }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ $admin->pivot->position }}</td>
                        <td>
                           @if($admin->pivot->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-info">View</a>
                            <a href="#" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No admins found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>
@endsection