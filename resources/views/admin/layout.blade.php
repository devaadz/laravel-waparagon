<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WA Paragon Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">WA Paragon Admin</h1>
            <ul class="flex space-x-4">
                <li><a href="{{ route('admin.forms.index') }}" class="hover:underline">Forms</a></li>
                <li><a href="{{ route('admin.stores.index') }}" class="hover:underline">Stores</a></li>
                <li><a href="{{ route('admin.form-stores.index') }}" class="hover:underline">Form Links</a></li>
                <li><a href="{{ route('admin.responses.index') }}" class="hover:underline">Responses</a></li>
                <li><a href="{{ route('admin.whatsapp.index') }}" class="hover:underline">WhatsApp</a></li>
                <li><a href="{{ route('admin.devices.index') }}" class="hover:underline">Devices</a></li>
            </ul>
        </div>
    </nav>

    {{-- <div class="container mx-auto p-4 min-h-screen"> --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    {{-- </div> --}}
    @yield('script')
</body>
</html>
