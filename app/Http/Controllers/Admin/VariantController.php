<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class VariantController extends Controller
{
    public function index() {
        $variants = ProductVariant::with('product')->get();
        return view('admin.variants.index', compact('variants'));
    }

    public function create() {
        $products = Product::all();
        return view('admin.variants.create', compact('products'));
    }

    public function store(Request $request) {
        $request->validate([
            'product_id' => 'required',
            'size' => 'required',
            'color' => 'required',
            'stock' => 'required|numeric',
            'variant_image' => 'nullable|image'
        ]);

        $image = null;

        if ($request->hasFile('variant_image')) {
            $image = $request->file('variant_image')->store('variants', 'public');
        }

        ProductVariant::create([
            'product_id' => $request->product_id,
            'size' => $request->size,
            'color' => $request->color,
            'stock' => $request->stock,
            'variant_image' => $image
        ]);

        return redirect()->route('admin.variants.index')->with('success', 'Variant Added');
    }

    public function edit(ProductVariant $variant) {
        $products = Product::all();
        return view('admin.variants.edit', compact('variant','products'));
    }

    public function update(Request $request, ProductVariant $variant) {
        $request->validate([
            'size' => 'required',
            'color' => 'required',
            'stock' => 'required|numeric',
        ]);

        $data = $request->all();

        if ($request->hasFile('variant_image')) {
            $data['variant_image'] = $request->file('variant_image')->store('variants', 'public');
        }

        $variant->update($data);

        return redirect()->route('admin.variants.index')->with('success', 'Variant Updated');
    }

    public function destroy(ProductVariant $variant) {
        $variant->delete();
        return back()->with('success', 'Variant Deleted');
    }
}
