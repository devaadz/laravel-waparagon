@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-4">{{ $form->name }}</h2>
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <p class="font-semibold">
                    <span class="px-3 py-1 rounded-full text-sm {{ $form->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($form->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Created</p>
                <p class="font-semibold">{{ $form->created_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        @if($form->description)
        <div class="mb-6">
            <p class="text-sm text-gray-600">Description</p>
            <p>{{ $form->description }}</p>
        </div>
        @endif
    </div>

    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-4">Form Fields</h3>
        @if($form->fields->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-4 py-2 text-left">Label</th>
                        <th class="border px-4 py-2 text-left">Type</th>
                        <th class="border px-4 py-2 text-left">Required</th>
                        <th class="border px-4 py-2 text-left">Sort Order</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($form->fields as $field)
                    <tr>
                        <td class="border px-4 py-2">{{ $field->label }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($field->type) }}</td>
                        <td class="border px-4 py-2">
                            @if($field->required)
                                <span class="text-green-600">Yes</span>
                            @else
                                <span class="text-gray-500">No</span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">{{ $field->sort_order }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500">No fields defined for this form.</p>
        @endif
    </div>

    <div class="flex items-center justify-between">
        <div class="flex space-x-2">
            <a href="{{ route('admin.forms.edit', $form->id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit Form
            </a>
            <a href="{{ route('admin.forms.stores', $form->id) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Link Stores
            </a>
        </div>
        <a href="{{ route('admin.forms.index') }}" class="text-gray-600 hover:text-gray-800">Back to Forms</a>
    </div>
</div>
@endsection
