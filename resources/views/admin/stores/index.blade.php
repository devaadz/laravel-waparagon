@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Stores</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.stores.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Store
            </a>
            <a href="{{ route('admin.form-stores.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Form Links
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Address</th>
                    <th class="px-4 py-2 text-left">WhatsApp Device</th>
                    <th class="px-4 py-2 text-left">Created</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $store->name }}</td>
                    <td class="px-4 py-2">{{ $store->whatsapp_device_id }}</td>
                    <td class="px-4 py-2">{{ $store->address }}</td>
                    <td class="px-4 py-2">{{ $store->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.stores.edit', $store) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                        <form method="POST" action="{{ route('admin.stores.destroy', $store) }}" class="inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No stores found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection