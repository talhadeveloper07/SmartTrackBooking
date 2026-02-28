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
        </div>

        <div class="row g-4 pb-5">


            {{-- EXISTING SERVICES --}}
            @forelse($services as $service)

                <div class="col-12 col-md-6 col-xl-4">
                    <div class="service-card card h-100">
                        <div class="card-body p-0">
                            @php
                                $durs = $service->durations ?? collect();

                                // durations (minutes)
                                $mins = $durs->pluck('duration_minutes')->filter()->map(fn($v) => (int) $v);
                                $durCount = $mins->count();
                                $minDuration = $durCount ? $mins->min() : null;
                                $maxDuration = $durCount ? $mins->max() : null;

                                // prices per duration (CHANGE 'price' if your column name is different)
                                $prices = $durs->pluck('price')->filter()->map(fn($v) => (float) $v);
                                $priceCount = $prices->count();
                                $minPrice = $priceCount ? $prices->min() : null;
                                $maxPrice = $priceCount ? $prices->max() : null;

                                // fallback service price
                                $singlePrice = $service->price ?? null;
                            @endphp

                            {{-- Header --}}
                            <div class="p-4 pb-3">
                                <h4 class="service-title mb-0">
                                    {{ $service->name }}
                                    <span class="text-muted fw-normal">
                                        (
                                        @if($durCount > 1)
                                            {{ $minDuration }}-{{ $maxDuration }} min
                                        @elseif($durCount === 1)
                                            {{ $minDuration }} min
                                        @elseif($singleDuration)
                                            {{ $singleDuration }} min
                                        @else
                                            —
                                        @endif
                                        )
                                    </span>
                                </h4>
                            </div>

                            <hr class="m-0">

                            {{-- Agents placeholder (optional) --}}
                            <div class="agent-area d-flex align-items-center justify-content-center">
                                <div class="agent-icon">
                                    <i class="fa fa-user"></i>
                                </div>
                            </div>

                            <hr class="m-0">

                            {{-- Details --}}
                            <div class="p-4 pt-3">

                                <div class="detail-row d-flex justify-content-between">
                                    <div class="text-start">Duration:</div>
                                    <div class="value">
                                        @if($durCount > 1)
                                            {{ $minDuration }} - {{ $maxDuration }} min
                                        @elseif($durCount === 1)
                                            {{ $minDuration }} min
                                        @elseif($singleDuration)
                                            {{ $singleDuration }} min
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>

                                <div class="detail-row d-flex justify-content-between">
                                    <div class="text-start">Price:</div>
                                    <div class="value">
                                        @if($priceCount > 1 && $minPrice != $maxPrice)
                                            ${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}
                                        @elseif($priceCount === 1)
                                            ${{ number_format($minPrice, 2) }}
                                        @elseif(!is_null($singlePrice))
                                            ${{ number_format((float) $singlePrice, 2) }}
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-row d-flex justify-content-between">
                                    <div class="text-start">Assigned to:</div>
                                    <div class="value">{{ $service->employees_count ?? 0 }} employees</div>
                                </div>
                            </div>

                            {{-- Footer --}}
                            <div class="p-3 pt-0">
                                <a href="{{ route('business.services.edit', [$business->slug, $service->id]) }}"
                                    class="btn btn-outline-primary w-100 rounded-pill py-2 fw-semibold">
                                    <i class="fa fa-pen me-2"></i>
                                    Edit Service
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                {{-- If no services, still show "New Service" tile below --}}
            @endforelse

            {{-- NEW SERVICE TILE --}}
            <div class="col-12 col-md-6 col-xl-4">
                <a href="{{ route('business.add.service', $business->slug) }}" class="new-service-tile h-100">
                    <div class="new-service-inner">
                        <div class="plus-wrap">
                            <span class="plus">+</span>
                        </div>
                        <div class="new-text">New Service</div>
                    </div>
                </a>
            </div>

        </div>

    </div>
@endsection