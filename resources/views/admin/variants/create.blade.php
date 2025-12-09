@extends('admin.layout')

@section('content')
<h1 class="text-3xl font-bold mb-5">Add Variant</h1>

<form action="{{ route('admin.variants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-2xl">
    @csrf

    <div>
        <label class="block text-sm font-medium mb-1">Product</label>
        <select name="product_id" class="w-full p-2 border rounded" required>
            <option value="">Select Product</option>
            @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
        @error('product_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Size</label>
        <input type="text" name="size" class="w-full p-2 border rounded" placeholder="e.g., 42, 9, M, L" required>
        @error('size') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Color</label>
        <input type="text" name="color" class="w-full p-2 border rounded" placeholder="e.g., Black, Red, Blue" required>
        @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Stock</label>
        <input type="number" name="stock" class="w-full p-2 border rounded" required>
        @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Variant Image (Color Preview)</label>
        <input type="file" name="variant_image" class="w-full p-2 border rounded">
        <p class="text-sm text-gray-500">Optional: Show the specific color variant</p>
        @error('variant_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex space-x-4">
        <button type="submit" class="bg-green-600 text-black px-4 py-2 rounded">Save Variant</button>
        <a href="{{ route('admin.variants.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
    </div>
</form>
@endsection