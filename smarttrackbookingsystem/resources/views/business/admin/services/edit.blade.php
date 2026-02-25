@extends('business.layouts.app')

@section('business_content')
    <div class="container">

        <div class="d-flex align-items-start gap-2">
            <a href="{{ route('business.services', $business->slug) }}" class="btn btn-xs btn-primary">Back</a>
            <h3 class="me-auto">Edit Service — {{ ucwords($business->name) }}</h3>
        </div>

        <form action="{{ route('business.services.update', [$business->slug, $service->id]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ================= BASIC DETAILS ================= --}}
            <div class="card mb-4">
                <div class="card-header"><strong>Service Details</strong></div>

                <div class="card-body">
                    <div class="row">

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Service Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $service->name) }}"
                                required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="default-select form-control wide">
                                <option value="active" {{ old('status', $service->status) == 'active' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="inactive" {{ old('status', $service->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3 col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3"
                                class="form-control">{{ old('description', $service->description) }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- OPTIONAL: If you added image field later --}}
                        {{--
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Service Image</label>
                            <input type="file" name="image" class="form-control">
                            @if($service->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$service->image) }}" width="90" class="rounded">
                            </div>
                            @endif
                        </div>
                        --}}

                    </div>
                </div>
            </div>

            {{-- ================= DURATIONS & PRICES ================= --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Durations & Prices</strong>
                    <button type="button" id="add-duration" class="btn btn-sm btn-primary">+ Add Duration</button>
                </div>

                <div class="card-body" id="duration-wrapper">

                    @php
                        $oldDurations = old('durations');
                        $durations = $oldDurations ?? $service->durations->map(function ($d) {
                            return [
                                'id' => $d->id,
                                'duration_name' => $d->duration_name,
                                'duration_minutes' => $d->duration_minutes,
                                'price' => $d->price,
                                'deposit' => $d->deposit,
                            ];
                        })->toArray();
                    @endphp

                    @foreach($durations as $i => $d)
                        <div class="duration-item border rounded p-3 mb-3">
                            <div class="row g-2 align-items-end">

                                {{-- existing duration id --}}
                                @if(!empty($d['id']))
                                    <input type="hidden" name="durations[{{ $i }}][id]" value="{{ $d['id'] }}">
                                @endif

                                <div class="col-md-3">
                                    <label class="form-label">Duration Name</label>
                                    <input type="text" name="durations[{{ $i }}][duration_name]" class="form-control"
                                        value="{{ $d['duration_name'] ?? '' }}" placeholder="Basic / Premium">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Duration (minutes) *</label>
                                    <input type="number" name="durations[{{ $i }}][duration_minutes]" class="form-control"
                                        value="{{ $d['duration_minutes'] ?? '' }}" required>
                                    @error("durations.$i.duration_minutes")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Price *</label>
                                    <input type="number" step="0.01" name="durations[{{ $i }}][price]" class="form-control"
                                        value="{{ $d['price'] ?? '' }}" required>
                                    @error("durations.$i.price")
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="durations[{{ $i }}][status]" class="form-control">
                                        <option value="active" {{ ($d['status'] ?? 'active') == 'active' ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="inactive" {{ ($d['status'] ?? '') == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-1 d-flex justify-content-end">
                                    <button type="button" class="btn btn-danger remove-duration" title="Remove">
                                        X
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Update Service</button>
                <a href="{{ route('business.services', $business->slug) }}" class="btn btn-light">Cancel</a>
            </div>

        </form>

    </div>


    <script>
        let durationIndex = {{ count($durations) }};

        document.getElementById('add-duration').addEventListener('click', function () {
            const wrapper = document.getElementById('duration-wrapper');

            const html = `
        <div class="duration-item border rounded p-3 mb-3">
            <div class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label">Duration Name</label>
                    <input type="text" name="durations[${durationIndex}][duration_name]" class="form-control" placeholder="Basic / Premium">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Duration (minutes) *</label>
                    <input type="number" name="durations[${durationIndex}][duration_minutes]" class="form-control" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Price *</label>
                    <input type="number" step="0.01" name="durations[${durationIndex}][price]" class="form-control" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Deposit</label>
                    <input type="number" step="0.01" name="durations[${durationIndex}][deposit]" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="durations[${durationIndex}][status]" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex justify-content-end">
                    <button type="button" class="btn btn-danger remove-duration" title="Remove">X</button>
                </div>

            </div>
        </div>`;

            wrapper.insertAdjacentHTML('beforeend', html);
            durationIndex++;
        });

        // Remove duration row (existing or new)
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-duration')) {
                e.target.closest('.duration-item').remove();
            }
        });
    </script>
@endsection