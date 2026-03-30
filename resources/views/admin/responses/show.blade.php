<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Response Details - WA Paragon Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('admin.layout')

    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Response Details</h2>
            <a href="{{ route('admin.responses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back to Responses</a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Email</h3>
                    <p class="text-gray-900">{{ $response->email }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Store</h3>
                    <p class="text-gray-900">{{ $response->store->name }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Form</h3>
                    <p class="text-gray-900">{{ $response->form->name }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-700">Submitted</h3>
                    <p class="text-gray-900">{{ $response->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <h3 class="text-xl font-bold mb-4">Answers</h3>
            <div class="space-y-4">
                @foreach($response->answers as $answer)
                <div class="border border-gray-200 rounded p-4">
                    <h4 class="font-semibold text-gray-700">{{ $answer->field->label }}</h4>
                    <p class="text-gray-900">
                        @if($answer->field->type === 'checkbox' && $answer->value)
                            @php
                                $values = json_decode($answer->value, true);
                                if (is_array($values)) {
                                    echo implode(', ', $values);
                                } else {
                                    echo $answer->value;
                                }
                            @endphp
                        @else
                            {{ $answer->value }}
                        @endif
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>