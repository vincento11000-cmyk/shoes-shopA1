@extends('admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-5">Add Category</h1>

<form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
    @csrf

    <label class="block">
        Name:
        <input type="text" name="name" class="w-full p-2 border rounded" required>
    </label>

    <button class="bg-green-600 text-black px-4 py-2 rounded">Save</button>
</form>
@endsection
