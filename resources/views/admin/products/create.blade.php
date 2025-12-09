@extends('admin.layout')

@section('content')
<h1 class="text-3xl font-bold mb-5">Add Product</h1>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-2xl">
    @csrf

    <div>
        <label class="block text-sm font-medium mb-1">Product Name</label>
        <input type="text" name="name" class="w-full p-2 border rounded" required>
        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Category</label>
        <select name="category_id" class="w-full p-2 border rounded" required>
            <option value="">Select Category</option>
            @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Base Price</label> {{-- Changed from Price --}}
        <input type="number" step="0.01" name="base_price" class="w-full p-2 border rounded" required>
        @error('base_price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Description</label>
        <textarea name="description" rows="3" class="w-full p-2 border rounded"></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Product Image</label>
        <input type="file" name="main_image" class="w-full p-2 border rounded" required> {{-- Changed from image to main_image --}}
        @error('main_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex space-x-4">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save Product</button>
        <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
    </div>
</form>
@endsection