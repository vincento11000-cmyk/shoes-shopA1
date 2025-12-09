@extends('admin.layout')

@section('content')
<h1 class="text-3xl font-bold mb-5">Edit Category</h1>

<form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-4 max-w-md">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium mb-1">Category Name</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full p-2 border rounded" required>
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex space-x-4">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Category</button>
        <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
    </div>
</form>
@endsection