<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Responses - WA Paragon Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('admin.layout')

    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Responses</h2>
            <a href="{{ route('admin.responses.export') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Export (Advanced)
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" class="mb-4 flex space-x-4">
                <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter by email" class="border border-gray-300 rounded px-3 py-2">
                <select name="form_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Forms</option>
                    @foreach(\App\Models\Form::all() as $form)
                        <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>{{ $form->name }}</option>
                    @endforeach
                </select>
                <select name="store_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Stores</option>
                    @foreach(\App\Models\Store::all() as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">ID</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Store</th>
                            <th class="px-4 py-2 text-left">Form</th>
                            <th class="px-4 py-2 text-left">Submitted</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($responses as $response)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $response->id }}</td>
                            <td class="px-4 py-2">{{ $response->email }}</td>
                            <td class="px-4 py-2">{{ $response->store->name }}</td>
                            <td class="px-4 py-2">{{ $response->form->name }}</td>
                            <td class="px-4 py-2">{{ $response->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.responses.show', $response) }}" class="text-blue-500 hover:underline">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-500">No responses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $responses->links() }}
        </div>
    </div>
</body>
</html>