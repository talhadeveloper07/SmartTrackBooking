@extends('business.layouts.app')

@section('title', 'Dashboard')

@section('business_content')
<div class="container-fluid">
    <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
        <h2 class="mb-3 me-auto">Welcome to {{ ucwords($business->name) }} dashboard</h2>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Admin Dashboard</a></li>
            </ol>
        </div>
    </div>	
    
    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="card-data me-2">
                        <h5>Properties for Sale</h5>
                        <h2 class="fs-40 font-w600">684</h2>
                    </div>
                    <div>
                        <span class="donut1" data-peity='{ "fill": ["var(--primary-color)", "rgba(var(--primary-color-rgb), 0.2)"]}'>5/6</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="card-data me-2">
                        <h5>Properties for Rent</h5>
                        <h2 class="fs-40 font-w600">546</h2>
                    </div>
                    <div>
                        <span class="donut1" data-peity='{ "fill": ["var(--secondary-color)", "rgba(var(--secondary-color-rgb), 0.2)"]}'>2/8</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="card-data me-2">
                        <h5>Total Customer</h5>
                        <h2 class="fs-40 font-w600">3,672</h2>
                    </div>
                    <div>
                        <span class="donut1" data-peity='{ "fill": ["var(--accent-color)", "rgba(var(--accent-color-rgb), 0.2)"]}'>5/8</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="card-data me-2">
                        <h5>Total City</h5>
                        <h2 class="fs-40 font-w600">75</h2>
                    </div>
                    <div>
                        <span class="donut1" data-peity='{ "fill": ["#333", "rgba(51, 51, 51, 0.2)"]}'>3/8</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Reinitialize charts when colors change
    function reinitializeCharts() {
        // Add your chart initialization code here
        console.log('Charts reinitialized with new colors');
    }

    // Listen for color changes
    window.addEventListener('storage', function(e) {
        if (e.key === 'settings_updated') {
            setTimeout(reinitializeCharts, 100);
        }
    });

    // Also listen for custom event
    $(document).on('settingsUpdated', function() {
        reinitializeCharts();
    });
</script>
@endpush
@endsection