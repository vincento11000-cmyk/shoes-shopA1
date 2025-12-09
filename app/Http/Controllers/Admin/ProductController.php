<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with('category')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create() {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'base_price' => 'required|numeric', // Changed from 'price' to 'base_price'
            'main_image' => 'required|image'    // Changed from 'image' to 'main_image'
        ]);

        $image = $request->file('main_image')->store('products', 'public'); // Changed variable name

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'base_price' => $request->base_price, // Changed from 'price' to 'base_price'
            'main_image' => $image // Changed from 'image' to 'main_image'
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product Added');
    }

    public function edit(Product $product) {
        $categories = Category::all();
        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product) {
        $request->validate([
            'name' => 'required',
            'base_price' => 'required|numeric', // Changed from 'price' to 'base_price'
        ]);

        $data = $request->all();

        if ($request->hasFile('main_image')) { // Changed from 'image' to 'main_image'
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product Updated');
    }

    public function destroy(Product $product) {
        $product->delete();
        return back()->with('success', 'Product Deleted');
    }
}