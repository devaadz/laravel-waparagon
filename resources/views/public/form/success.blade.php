@extends('public.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6 text-center">
    <div class="text-green-500 text-6xl mb-4">✓</div>
    <h1 class="text-2xl font-bold mb-4">Thank You!</h1>
    <p class="text-gray-600 mb-4">Your form has been submitted successfully. You will receive a WhatsApp message confirmation shortly.</p>
    <a href="{{ url('/') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Back to Home
    </a>
</div>
@endsection