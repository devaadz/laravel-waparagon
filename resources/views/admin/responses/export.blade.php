@extends('admin.layout')

@section('title', 'Export Responses')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Export Responses to Excel</h1>
        <a href="{{ route('admin.responses.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            ← Back to Responses
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.responses.export') }}" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Form</label>
                <select name="form_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Forms</option>
@foreach($forms as $id => $name)
    <option value="{{ $id }}" {{ request('form_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
@endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Store / Toko / Location</label>
                <select name="store_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Stores</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                            {{ $store->name }} ({{ Str::limit($store->address, 30) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ request('email') }}" placeholder="Search by email..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Limit</label>
                <select name="limit" id="limitSelect" onchange="toggleCustomLimit()" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All (no limit)</option>
                    <option value="10" {{ request('limit') == '10' ? 'selected' : '' }}>10</option>
                    <option value="100" {{ request('limit') == '100' ? 'selected' : '' }}>100</option>
                    <option value="1000" {{ request('limit') == '1000' ? 'selected' : '' }}>1000</option>
                    <option value="custom" {{ request('limit') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
                <input type="number" name="custom_limit" id="customLimit" value="{{ request('custom_limit') }}" min="1" 
                       placeholder="Enter number" class="w-full px-3 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 hidden">
            </div>
        </div>

        <div class="flex justify-end mt-8 space-x-3">
            <a href="{{ route('admin.responses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                Cancel
            </a>
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                📊 Export to Excel
            </button>
        </div>
    </form>

    @if(request()->filled('form_id') || request()->filled('store_id'))
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded">
            <p class="text-sm text-blue-800">
                Preview: Export will include all form names, fields (dynamic columns), store details, email, dates, and all response values per field.
            </p>
        </div>
    @endif
</div>

<script>
function toggleCustomLimit() {
    const select = document.getElementById('limitSelect');
    const customInput = document.getElementById('customLimit');
    if (select.value === 'custom') {
        customInput.classList.remove('hidden');
        customInput.required = true;
    } else {
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    }
}
</script>
@endsection
