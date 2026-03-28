@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-md">
    <h2 class="text-2xl font-bold mb-4">Add Field to {{ $form->name }}</h2>

    <form method="POST" action="{{ route('admin.forms.fields.store') }}">
        @csrf
        <input type="hidden" name="form_id" value="{{ $form->id }}">

        <div class="mb-4">
            <label for="label" class="block text-gray-700 text-sm font-bold mb-2">Label</label>
            <input type="text" name="label" id="label" value="{{ old('label') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            @error('label')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Type</label>
            <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="text" {{ old('type') === 'text' ? 'selected' : '' }}>Text</option>
                <option value="number" {{ old('type') === 'number' ? 'selected' : '' }}>Number</option>
                <option value="date" {{ old('type') === 'date' ? 'selected' : '' }}>Date</option>
                <option value="radio" {{ old('type') === 'radio' ? 'selected' : '' }}>Radio</option>
                <option value="select" {{ old('type') === 'select' ? 'selected' : '' }}>Select</option>
                <option value="textarea" {{ old('type') === 'textarea' ? 'selected' : '' }}>Textarea</option>
                <option value="email" {{ old('type') === 'email' ? 'selected' : '' }}>Email</option>
                <option value="tel" {{ old('type') === 'tel' ? 'selected' : '' }}>Tel</option>
            </select>
            @error('type')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="placeholder" class="block text-gray-700 text-sm font-bold mb-2">Placeholder</label>
            <input type="text" name="placeholder" id="placeholder" value="{{ old('placeholder') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('placeholder')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                <input type="checkbox" name="required" value="1" {{ old('required') ? 'checked' : '' }} class="mr-2">
                Required
            </label>
            @error('required')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4" id="options-container" style="display: none;">
            <label for="options" class="block text-gray-700 text-sm font-bold mb-2">Options (one per line)</label>
            <textarea name="options[]" id="options" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('options') ? implode("\n", old('options')) : '' }}</textarea>
            @error('options')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="sort_order" class="block text-gray-700 text-sm font-bold mb-2">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('sort_order')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Field
            </button>
            <a href="{{ route('admin.forms.fields.index', ['form_id' => $form->id]) }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</div>

<script>
document.getElementById('type').addEventListener('change', function() {
    const optionsContainer = document.getElementById('options-container');
    if (this.value === 'radio' || this.value === 'select') {
        optionsContainer.style.display = 'block';
    } else {
        optionsContainer.style.display = 'none';
    }
});
</script>
@endsection