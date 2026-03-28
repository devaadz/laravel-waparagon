@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Forms</h2>
        <div class="space-x-2">
            <a href="{{ route('admin.forms.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add Form
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
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Created</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $form)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $form->name }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded text-xs {{ $form->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($form->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">{{ $form->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.forms.show', $form) }}" class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                        <a href="{{ route('admin.forms.edit', $form) }}" class="text-blue-600 hover:text-blue-900 mr-2">Edit</a>
                        <a href="{{ route('public.form.preview', $form) }}" target="_blank" class="text-green-600 hover:text-green-900 mr-2">Preview</a>
                        <a href="{{ route('admin.forms.fields.index', $form) }}" class="text-green-600 hover:text-green-900 mr-2">Fields</a>
                        <form method="POST" action="{{ route('admin.forms.destroy', $form->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No forms found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection