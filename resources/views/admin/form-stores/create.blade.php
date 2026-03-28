@extends('admin.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-3xl font-bold mb-8">Create Form-Store Connection</h1>
    
    <form method="POST" action="{{ route('admin.form-stores.store') }}">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <label class="block text-sm font-bold mb-2">Form</label>
                <select name="form_id" class="w-full p-3 border rounded-lg">
                    @foreach($forms as $form)
                        <option value="{{ $form->id }}">{{ $form->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold mb-2">Store</label>
                <select name="store_id" class="w-full p-3 border rounded-lg">
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
            Create Connection
        </button>
    </form>
</div>
@endsection
