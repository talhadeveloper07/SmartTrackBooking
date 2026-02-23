<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

           <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        	<link href="/vendor/jquery-nice-select/css/nice-select.css" rel="stylesheet">
	<link rel="stylesheet" href="/vendor/dotted-map/css/contrib/jquery.smallipop-0.3.0.min.css" type="text/css" media="all">
      <link href="/css/style.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <main class="vh-100">
            @yield('content')
        </main>
    </div>
</body>

 <script src="/vendor/global/global.min.js"></script>
	<script src="/vendor/chart.js/Chart.bundle.min.js"></script>
	<script src="/vendor/jquery-nice-select/js/jquery.nice-select.min.js"></script>
	
	<!-- Apex Chart -->
	<script src="/vendor/apexchart/apexchart.js"></script>
	
	<!-- Chart piety plugin files -->
    <script src="/vendor/peity/jquery.peity.min.js"></script>
	
	<!-- Dashboard 1 -->
	<script src="./js/dashboard/dashboard-1.js"></script>
	
	<!-- JS for dotted map -->
    <script src="/vendor/dotted-map/js/contrib/jquery.smallipop-0.3.0.min.js"></script>
    <script src="/vendor/dotted-map/js/contrib/suntimes.js"></script>
    <script src="/vendor/dotted-map/js/contrib/color-0.4.1.js"></script>
	
	<script src="/vendor/dotted-map/js/world.js"></script>
    <script src="/vendor/dotted-map/js/smallimap.js"></script>
    <script src="/js/dashboard/dotted-map-init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
	
	
    <script src="/js/custom.min.js"></script>
	<script src="/js/deznav-init.js"></script>

</html>
