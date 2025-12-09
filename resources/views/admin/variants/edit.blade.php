@extends('admin.layout')

@section('content')
<h1 class="text-3xl font-bold mb-5">Edit Variant</h1>

<form action="{{ route('admin.variants.update', $variant->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-2xl">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium mb-1">Product</label>
        <select name="product_id" class="w-full p-2 border rounded" required>
            @foreach ($products as $product)
            <option value="{{ $product->id }}" {{ $variant->product_id == $product->id ? 'selected' : '' }}>
                {{ $product->name }}
            </option>
            @endforeach
        </select>
        @error('product_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Size</label>
        <input type="text" name="size" value="{{ old('size', $variant->size) }}" class="w-full p-2 border rounded" required>
        @error('size') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Color</label>
        <input type="text" name="color" value="{{ old('color', $variant->color) }}" class="w-full p-2 border rounded" required>
        @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $variant->stock) }}" class="w-full p-2 border rounded" required>
        @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Variant Image</label>
        @if($variant->variant_image)
        <div class="mb-2">
            <img src="{{ asset('storage/'.$variant->variant_image) }}" class="w-20 h-20 rounded">
            <p class="text-sm text-gray-500">Current Image</p>
        </div>
        @endif
        <input type="file" name="variant_image" class="w-full p-2 border rounded">
        <p class="text-sm text-gray-500">Leave empty to keep current image</p>
        @error('variant_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <div class="flex space-x-4">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update Variant</button>
        <a href="{{ route('admin.variants.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
    </div>
</form>
@endsection