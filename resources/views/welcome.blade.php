<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WA Paragon Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">WA Paragon Admin</h1>
            <ul class="flex space-x-4">
                <li><a href="{{ route('admin.forms.index') }}" class="hover:underline">Forms</a></li>
                <li><a href="{{ route('admin.stores.index') }}" class="hover:underline">Stores</a></li>
                <li><a href="{{ route('admin.responses.index') }}" class="hover:underline">Responses</a></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto p-4">
        <h2 class="text-3xl font-bold mb-6">Dashboard</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Total Forms</h3>
                <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Form::count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Total Stores</h3>
                <p class="text-3xl font-bold text-green-600">{{ \App\Models\Store::count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Total Responses</h3>
                <p class="text-3xl font-bold text-purple-600">{{ \App\Models\Response::count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-700">Today's Responses</h3>
                <p class="text-3xl font-bold text-orange-600">{{ \App\Models\Response::whereDate('created_at', today())->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-xl font-bold mb-4">Recent Responses</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Store</th>
                            <th class="px-4 py-2 text-left">Form</th>
                            <th class="px-4 py-2 text-left">Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(\App\Models\Response::with(['form', 'store'])->latest()->take(10)->get() as $response)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $response->email }}</td>
                            <td class="px-4 py-2">{{ $response->store->name }}</td>
                            <td class="px-4 py-2">{{ $response->form->name }}</td>
                            <td class="px-4 py-2">{{ $response->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">No responses yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
