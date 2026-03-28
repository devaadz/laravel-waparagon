@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Link Stores to Form: {{ $form->name }}</h1>
            <p class="text-gray-600 mt-2">Connect this form to stores for unique form URLs ({{ count($linkedStoreIds) }} linked)</p>
        </div>
        <a href="{{ route('admin.forms.edit', $form) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            ← Back to Form
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Available Stores -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">Available Stores ({{ $stores->whereNotIn('id', $linkedStoreIds)->count() }})</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($stores->whereNotIn('id', $linkedStoreIds) as $store)
            <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                <h3 class="text-xl font-bold mb-2">{{ $store->name }}</h3>
                <p class="text-gray-600 mb-4">{{ $store->phone ?? 'No phone' }}</p>
                <form method="POST" action="{{ route('admin.forms.stores.link', $form) }}" class="inline">
                    @csrf
                    <input type="hidden" name="store_id" value="{{ $store->id }}">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                        Link Store
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Linked Stores -->
    <div>
        <h2 class="text-2xl font-bold mb-4">Linked Stores ({{ count($linkedStoreIds) }})</h2>
        @if($form->formStores->count())
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-6 py-3 text-left">Link Name</th>
                        <th class="border px-6 py-3 text-left">Store</th>
                        <th class="border px-6 py-3 text-left">Slug</th>
                        <th class="border px-6 py-3 text-left">Form URL</th>
                        <th class="border px-6 py-3 text-left">QR</th>
                        <th class="border px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($form->formStores as $formStore)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-6 py-4 font-semibold">{{ $formStore->name }}</td>
                        <td class="border px-6 py-4">
                            <span class="font-medium">{{ $formStore->store->name }}</span>
                        </td>
                        <td class="border px-6 py-4 font-mono text-sm bg-gray-50">{{ $formStore->custom_url_slug }}</td>
                        <td class="border px-6 py-4">
                            <a href="/form/{{ $formStore->custom_url_slug }}" target="_blank" class="text-blue-500 hover:underline">
                                /form/{{ $formStore->custom_url_slug }}
                            </a>
                        </td>
                        <td class="border px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('public.form.store.qr', $formStore->custom_url_slug) }}" target="_blank">
                                    <img src="{{ route('public.form.store.qr', $formStore->custom_url_slug) }}" alt="QR" class="w-12 h-12">
                                </a>
                                <a href="{{ route('public.form.store.qr.download', $formStore->custom_url_slug) }}" download class="text-green-500 hover:underline">DL</a>
                            </div>
                        </td>
                        <td class="border px-6 py-4">
                            <form method="POST" action="{{ route('admin.forms.stores.unlink', [$form, $formStore->store_id]) }}" class="inline" onsubmit="return confirm('Unlink {{ $formStore->store->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                    Unlink
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500">No stores linked yet. Link stores above to create unique form URLs.</p>
        @endif
    </div>
</div>
@endsection

