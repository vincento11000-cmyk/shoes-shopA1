@extends('admin.layout')

@section('title', 'Categories')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
            <p class="text-gray-600 mt-1">Manage product categories</p>
        </div>
        
        <a href="{{ route('admin.categories.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            + Add Category
        </a>
    </div>

    <div class="bg-white rounded-lg shadow border">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="py-3 px-4 text-left font-medium text-gray-700">Name</th>
                    <th class="py-3 px-4 text-left font-medium text-gray-700">Products</th>
                    <th class="py-3 px-4 text-left font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <span class="font-medium">{{ $category->name }}</span>
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        @if(isset($category->products_count))
                            {{ $category->products_count }} products
                        @else
                            {{ $category->products->count() }} products
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-3">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                Edit
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Delete this category? Products in this category will become uncategorized.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                
                @if($categories->isEmpty())
                <tr>
                    <td colspan="3" class="py-8 px-4 text-center text-gray-500">
                        No categories found
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection