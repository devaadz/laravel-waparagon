@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Fields for {{ $form->name }}</h2>
        <a href="{{ route('admin.forms.fields.create', ['form_id' => $form->id]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Field
        </a>
    </div>

    <div class="mb-4">
        <a href="{{ route('admin.forms.index') }}" class="text-blue-600 hover:text-blue-900">&larr; Back to Forms</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-2 text-left">Label</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-left">Required</th>
                    <th class="px-4 py-2 text-left">Order</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fields as $field)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $field->label }}</td>
                    <td class="px-4 py-2">{{ ucfirst($field->type) }}</td>
                    <td class="px-4 py-2">{{ $field->required ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-2">{{ $field->sort_order }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.forms.fields.edit', $field) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                        <form method="POST" action="{{ route('admin.forms.fields.destroy', $field) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">No fields found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection