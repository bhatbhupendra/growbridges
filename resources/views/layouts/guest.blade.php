<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
.brand-row {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 08px;
}

.brand-logo {
    position: relative;
    width: 100%;
    max-width: 60px;
    aspect-ratio: 1 / 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.brand-title-wrap h1 {
    margin: 0;
    font-size: 28px;
    line-height: 1.1;
    color: #111827;
    font-weight: 800;
}

.brand-subtitle {
    margin-top: 4px;
    color: #6b7280;
    font-size: 14px;
    font-weight: 500;
}
</style>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="brand-row">
            <div class="brand-logo">
                <img src="{{ asset('images/grow.png') }}" alt="Company Logo" class="center-logo">
            </div>
            <div class="brand-title-wrap">
                <h1>Grow Bridges Pvt. Ltd.</h1>
                <div class="brand-subtitle">Connecting to Grow.</div>
            </div>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>