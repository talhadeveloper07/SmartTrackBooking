@extends('business.layouts.app')
@section('business_content')

    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Add New Service</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Add New Service</a></li>
                </ol>
            </div>
        </div>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <form action="{{ route('business.insert.services', $business->slug) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            {{-- ================= BASIC DETAILS ================= --}}
                            <div class="card mb-4">
                                <div class="card-header"><strong>Service Details</strong></div>

                                <div class="card-body">
                                    <div class="row">

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Service Name *</label>
                                            <input type="text" name="name" class="form-control" placeholder="Hair Cut"
                                                required>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Status</label>
                                            <select name="status" class="default-select form-control wide">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-12">
                                            <label class="form-label">Description</label>
                                            <textarea name="description" rows="3" class="form-control"></textarea>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label class="form-label">Service Image</label>
                                            <input type="file" name="image" class="form-control">
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- ================= DURATIONS & PRICES ================= --}}
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <strong>Durations & Prices</strong>
                                    <button type="button" id="add-duration" class="btn btn-sm btn-primary">+ Add
                                        Duration</button>
                                </div>

                                <div class="card-body" id="duration-wrapper">

                                    {{-- First duration row --}}
                                    <div class="duration-item border rounded p-3 mb-3">
                                        <div class="row">

                                            <div class="col-md-3">
                                                <label>Duration Name</label>
                                                <input type="text" name="durations[0][duration_name]" class="form-control"
                                                    placeholder="Basic / Premium">
                                            </div>

                                            <div class="col-md-2">
                                                <label>Duration (minutes) *</label>
                                                <input type="number" name="durations[0][duration_minutes]"
                                                    class="form-control" required>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Price *</label>
                                                <input type="number" step="0.01" name="durations[0][price]"
                                                    class="form-control" required>
                                            </div>

                                            <div class="col-md-2">
                                                <label>Deposit</label>
                                                <input type="number" step="0.01" name="durations[0][deposit]"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Status</label>
                                                <select name="durations[0][status]"
                                                    class="default-select form-control wide">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>

                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-duration">X</button>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="btn btn-success">Create Service</button>
                                <a href="{{ route('business.dashboard', $business->slug) }}"
                                    class="btn btn-light">Cancel</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        let index = 1;

        document.getElementById('add-duration').addEventListener('click', function () {

            let html = `
        <div class="duration-item border rounded p-3 mb-3">
            <div class="row">

                <div class="col-md-3">
                    <input type="text" name="durations[${index}][duration_name]" class="form-control" placeholder="Basic / Premium">
                </div>

                <div class="col-md-2">
                    <input type="number" name="durations[${index}][duration_minutes]" class="form-control" required>
                </div>

                <div class="col-md-2">
                    <input type="number" step="0.01" name="durations[${index}][price]" class="form-control" required>
                </div>

                <div class="col-md-2">
                    <input type="number" step="0.01" name="durations[${index}][deposit]" class="form-control">
                </div>

                <div class="col-md-2">
                    <select name="durations[${index}][status]" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-danger remove-duration">X</button>
                </div>

            </div>
        </div>`;

            document.getElementById('duration-wrapper').insertAdjacentHTML('beforeend', html);
            index++;
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-duration')) {
                e.target.closest('.duration-item').remove();
            }
        });
    </script>
@endsection