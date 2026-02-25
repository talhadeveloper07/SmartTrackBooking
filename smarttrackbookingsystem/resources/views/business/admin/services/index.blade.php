@extends('business.layouts.app')

@section('business_content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-3">
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

        <h3 class="me-auto">Services</h3>

        <a href="{{ route('business.add.service', $business->slug) }}" class="btn btn-primary">
            Add Service
        </a>
    </div>

    <div class="row g-4">
        @forelse($services as $service)

            @php
                $defaultDuration = $service->durations->first(); // smallest duration
                $durationText = $defaultDuration ? $defaultDuration->duration_minutes.' min' : '-';
                $priceText = $defaultDuration ? '$'.number_format($defaultDuration->price, 2) : '-';
                $agentsText = '—'; // later when you add agents
            @endphp

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">

                    <div class="p-4 border-bottom bg-white">
                        <div class="d-flex align-items-start justify-content-between">
                            <h5 class="mb-0">{{ ucwords($service->name) }}</h5>
                        </div>
                    </div>

                    <div class="p-4">
                        {{-- Agents icon placeholder like screenshot --}}
                        <div class="d-flex align-items-center justify-content-center py-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                                 style="width:52px;height:52px;">
                                <i class="fa fa-user text-muted"></i>
                            </div>
                        </div>

                        <div class="row small text-muted">
                            <div class="col-6 mb-2">Agents:</div>
                            <div class="col-6 mb-2 text-end fw-semibold text-dark">{{ $agentsText }}</div>

                            <div class="col-6 mb-2">Duration:</div>
                            <div class="col-6 mb-2 text-end fw-semibold text-dark">{{ $durationText }}</div>

                            <div class="col-6 mb-2">Price:</div>
                            <div class="col-6 mb-2 text-end fw-semibold text-dark">{{ $priceText }}</div>

                        </div>
                    </div>

                    <div class="px-3">
                        <div class="d-flex gap-2">
                            <a href="{{ route('business.services.edit', [$business->slug, $service->id]) }}"
                               class="btn btn-outline-primary w-100 rounded-3">
                                <i class="fa fa-pen me-2"></i> Edit Service
                            </a>

                            <!-- <form action="{{ route('business.services.destroy', [$business->slug, $service->id]) }}"
                                  method="POST" class="w-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-outline-danger w-100 rounded-3"
                                        onclick="return confirm('Delete this service?')">
                                    <i class="fa fa-trash me-2"></i> Delete
                                </button>
                            </form> -->
                        </div>
                    </div>

                </div>
            </div>

        @empty
            <div class="col-12">
                <div class="alert alert-info">No services found.</div>
            </div>
        @endforelse
    </div>

</div>
@endsection