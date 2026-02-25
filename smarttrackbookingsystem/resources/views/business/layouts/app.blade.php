<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $business->name ?? 'Business Dashboard')</title>

    @php
        $favicon = App\Helpers\BusinessSettingsHelper::getFavicon($business ?? null);
        $colors = App\Helpers\BusinessSettingsHelper::getColors($business ?? null);
        $fontFamily = App\Helpers\BusinessSettingsHelper::get($business ?? null, 'font_family', 'Inter, sans-serif');
    @endphp

    @if($favicon)
        <link rel="icon" type="image/png" href="{{ $favicon }}">
    @endif

    {{-- Load Google Fonts dynamically --}}
    @php
        $fontName = explode(',', $fontFamily)[0];
        $fontName = str_replace(' ', '+', trim($fontName));
        if($fontName != 'Inter' && $fontName != 'Arial' && $fontName != 'Helvetica') {
            echo '<link href="https://fonts.googleapis.com/css2?family=' . $fontName . ':wght@300;400;500;600;700&display=swap" rel="stylesheet">';
        }
    @endphp
        	<link href="/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
	<link rel="stylesheet" href="/vendor/dotted-map/css/contrib/jquery.smallipop-0.3.0.min.css" type="text/css" media="all">
      <link href="/css/style.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style id="dynamic-styles">
        :root {
            --primary-color: {{ $colors['primary'] }};
            --primary-color-rgb: {{ $colors['primary_rgb'] }};
            --secondary-color: {{ $colors['secondary'] }};
            --secondary-color-rgb: {{ $colors['secondary_rgb'] }};
            --accent-color: {{ $colors['accent'] }};
            --accent-color-rgb: {{ $colors['accent_rgb'] }};
            --font-family: {{ $fontFamily }};
        }
        
        /* Apply font family to all elements */
        * {
            font-family: var(--font-family) !important;
        }
        
        body { 
            font-family: var(--font-family) !important; 
        }
        
        /* Override any Bootstrap defaults */
        h1, h2, h3, h4, h5, h6, p, span, a, div, button, input, select, textarea {
            font-family: var(--font-family) !important;
        }
        
        .bg-primary, .btn-primary, .badge.bg-primary { 
            background-color: var(--primary-color) !important; 
        }
        
        .btn-primary { 
            border-color: var(--primary-color); 
        }
        
        .btn-primary:hover { 
            background-color: rgba(var(--primary-color-rgb), 0.8) !important; 
            border-color: rgba(var(--primary-color-rgb), 0.9); 
        }
        
        .text-primary { 
            color: var(--primary-color) !important; 
        }
        
        .border-primary { 
            border-color: var(--primary-color) !important; 
        }
        
        .rect-primary-rect { 
            fill: var(--primary-color) !important; 
        }
        
        .svg-title-path { 
            fill: var(--primary-color) !important; 
        }
        
        .badge.bg-success { 
            background-color: var(--secondary-color) !important; 
        }
        
        .badge.bg-orange { 
            background-color: var(--accent-color) !important; 
        }
        
        .list-group-item.active {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        .metismenu .active > a {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .metismenu a:hover {
            color: var(--primary-color) !important;
        }
    </style>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link href="/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    
    <script src="/vendor/global/global.min.js"></script>
    <script src="/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
    <script src="/js/custom.min.js"></script>
<<<<<<< Updated upstream
    <script src="/js/deznav-init.js"></script>
    
    <script>
        // Function to update colors and font dynamically
        function updateDynamicSettings(colors, fontFamily) {
            const root = document.documentElement;
            
            // Update colors
            root.style.setProperty('--primary-color', colors.primary);
            root.style.setProperty('--primary-color-rgb', colors.primary_rgb);
            root.style.setProperty('--secondary-color', colors.secondary);
            root.style.setProperty('--secondary-color-rgb', colors.secondary_rgb);
            root.style.setProperty('--accent-color', colors.accent);
            root.style.setProperty('--accent-color-rgb', colors.accent_rgb);
            
            // Update font family
            if (fontFamily) {
                root.style.setProperty('--font-family', fontFamily);
                
                // Apply to all elements
                document.querySelectorAll('*').forEach(el => {
                    el.style.fontFamily = fontFamily;
                });
            }
            
            // Update SVG elements
            document.querySelectorAll('.svg-title-path, .rect-primary-rect').forEach(el => {
                el.style.fill = colors.primary;
            });
            
            // Update plus box
            const plusBox = document.querySelector('.plus-box');
            if (plusBox) {
                plusBox.style.background = `linear-gradient(135deg, ${colors.primary} 0%, ${colors.secondary} 100%)`;
            }
            
            console.log('Settings updated:', { colors, fontFamily });
        }

        // Function to fetch latest settings
        function refreshSettings() {
            fetch('{{ route("business.settings.data", $business->slug ?? "") }}')
                .then(response => response.json())
                .then(data => {
                    updateDynamicSettings(data.colors, data.font_family);
                })
                .catch(error => console.error('Error:', error));
        }

        // Listen for storage events
        window.addEventListener('storage', function(e) {
            if (e.key === 'settings_updated') {
                refreshSettings();
            }
        });

        window.updateDynamicSettings = updateDynamicSettings;
        window.refreshSettings = refreshSettings;
    </script>
    
    @stack('scripts')
</body>
</html>
=======
	<script src="/js/deznav-init.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
</html>
>>>>>>> Stashed changes
