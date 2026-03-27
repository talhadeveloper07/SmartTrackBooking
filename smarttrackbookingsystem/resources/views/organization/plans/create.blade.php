@extends('organization.layouts.app')

@section('organization_content')

<div class="container-fluid">

<h3>Create Plan</h3>

<form method="POST" action="{{ route('org.plans.store') }}">

@csrf

<div class="mb-3">
<label>Name</label>
<input type="text" name="name" class="form-control">
</div>

<div class="mb-3">
<label>Description</label>
<textarea name="description" class="form-control"></textarea>
</div>

<div class="mb-3">
<label>Price</label>
<input type="number" step="0.01" name="price" class="form-control">
</div>

<div class="mb-3">
<label>Max Employees</label>
<input type="number" name="max_employees" class="form-control">
</div>

<div class="mb-3">
<label>Max Services</label>
<input type="number" name="max_services" class="form-control">
</div>

<div class="mb-3">
<label>Max Bookings</label>
<input type="number" name="max_bookings" class="form-control">
</div>

<button class="btn btn-primary">Save Plan</button>

</form>

</div>

@endsection