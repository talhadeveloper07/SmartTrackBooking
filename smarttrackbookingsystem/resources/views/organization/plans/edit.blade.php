@extends('organization.layouts.app')

@section('organization_content')

<div class="container">

<h3>Edit Plan</h3>

<form method="POST"
      action="{{ route('org.plans.update',$plan->id) }}">

@csrf
@method('PUT')

<div class="mb-3">
<label>Name</label>
<input type="text"
       name="name"
       value="{{ $plan->name }}"
       class="form-control">
</div>

<div class="mb-3">
<label>Description</label>
<textarea name="description"
          class="form-control">{{ $plan->description }}</textarea>
</div>

<div class="mb-3">
<label>Price</label>
<input type="number"
       step="0.01"
       name="price"
       value="{{ $plan->price }}"
       class="form-control">
</div>

<div class="mb-3">
<label>Max Employees</label>
<input type="number"
       name="max_employees"
       value="{{ $plan->max_employees }}"
       class="form-control">
</div>

<div class="mb-3">
<label>Max Services</label>
<input type="number"
       name="max_services"
       value="{{ $plan->max_services }}"
       class="form-control">
</div>

<div class="mb-3">
<label>Max Bookings</label>
<input type="number"
       name="max_bookings"
       value="{{ $plan->max_bookings }}"
       class="form-control">
</div>

<button class="btn btn-primary">
Update Plan
</button>

</form>

</div>

@endsection