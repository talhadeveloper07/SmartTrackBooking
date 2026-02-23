{{-- resources/views/business/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', $business->name ?? 'Business Dashboard')</title>

    {{-- Dynamic Favicon --}}
    @php
        $favicon = App\Helpers\BusinessSettingsHelper::getFavicon($business ?? null);
    @endphp
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ $favicon }}">
    @endif

    {{-- Dynamic Colors --}}
    @php
        $colors = App\Helpers\BusinessSettingsHelper::getColors($business ?? null);
        $fontFamily = App\Helpers\BusinessSettingsHelper::get($business ?? null, 'font_family', 'Inter, sans-serif');
    @endphp

    <style>
        :root {
            --primary-color: {{ $colors['primary'] }};
            --primary-color-rgb: {{ implode(',', sscanf($colors['primary'], "#%02x%02x%02x")) }};
            --secondary-color: {{ $colors['secondary'] }};
            --accent-color: {{ $colors['accent'] }};
            --font-family: {{ $fontFamily }};
        }

        body {
            font-family: var(--font-family);
        }

        /* Override Bootstrap primary colors */
        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: rgba(var(--primary-color-rgb), 0.8);
            border-color: rgba(var(--primary-color-rgb), 0.9);
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .border-primary {
            border-color: var(--primary-color) !important;
        }

        /* Override any theme-specific colors */
        .rect-primary-rect {
            fill: var(--primary-color) !important;
        }
        
        .svg-title-path {
            fill: var(--primary-color) !important;
        }

        /* Notification badges */
        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .badge.bg-success {
            background-color: var(--secondary-color) !important;
        }

        .badge.bg-orange {
            background-color: var(--accent-color) !important;
        }
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <link href="/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/dotted-map/css/contrib/jquery.smallipop-0.3.0.min.css" type="text/css" media="all">
    <link href="/css/style.css" rel="stylesheet">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    @stack('styles')
</head>
<body>

    <div id="preloader">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>
    
    <div id="main-wrapper">
        @include('business.layouts.navbar')
        @include('business.layouts.header')
        @include('business.layouts.sidebar')
        
        <div class="content-body">
            @yield('business_content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <script src="/vendor/global/global.min.js"></script>
    <script src="/vendor/chart.js/Chart.bundle.min.js"></script>
    <script src="/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
    
    <!-- Apex Chart -->
    <script src="/vendor/apexchart/apexchart.js"></script>
    
    <!-- Chart piety plugin files -->
    <script src="/vendor/peity/jquery.peity.min.js"></script>
    
    <!-- Dashboard 1 -->
    <script src="/js/dashboard/dashboard-1.js"></script>
    
    <!-- JS for dotted map -->
    <script src="/vendor/dotted-map/js/contrib/jquery.smallipop-0.3.0.min.js"></script>
    <script src="/vendor/dotted-map/js/contrib/suntimes.js"></script>
    <script src="/vendor/dotted-map/js/contrib/color-0.4.1.js"></script>
    
    <script src="/vendor/dotted-map/js/world.js"></script>
    <script src="/vendor/dotted-map/js/smallimap.js"></script>
    <script src="/js/dashboard/dotted-map-init.js"></script>
    
    <script src="/js/custom.min.js"></script>
    <script src="/js/deznav-init.js"></script>
    
    @stack('scripts')
</body>
</html>