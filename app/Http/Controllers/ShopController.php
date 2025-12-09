<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function home()
    {
        $featured = Product::with('variants')->latest()->take(8)->get();
        $new = Product::with('variants')->orderBy('created_at', 'desc')->take(8)->get();
        $best = Product::with('variants')->take(8)->get();

        return view('home', compact('featured', 'new', 'best'));
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants']);

        // 🔍 SEARCH BY NAME
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // 🏷️ FILTER BY CATEGORY
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 💰 FILTER BY PRICE RANGE
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // 📏 FILTER BY SIZE
        if ($request->filled('size')) {
            $query->whereHas('variants', function($q) use ($request){
                $q->where('size', $request->size);
            });
        }

        // 🎨 FILTER BY COLOR
        if ($request->filled('color')) {
            $query->whereHas('variants', function($q) use ($request){
                $q->where('color', $request->color);
            });
        }

        $products = $query->paginate(12); // ✅ PAGINATION ADDED

        // 📊 FILTER DROPDOWN DATA
        $availableSizes = ProductVariant::distinct()->pluck('size')->sort();
        $availableColors = ProductVariant::distinct()->pluck('color')->sort();
        $categories = Category::all();

        return view('shop.index', compact(
            'products', 
            'availableSizes', 
            'availableColors', 
            'categories'
        ));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'variants'])->findOrFail($id);
        $variants = ProductVariant::where('product_id', $id)->get();

        $colors = $variants->groupBy('color')->map(function($group) {
            return [
                'color' => $group->first()->color,
                'image' => $group->first()->variant_image ?: $group->first()->product->main_image,
            ];
        });

        $sizes = $variants->groupBy('size')->map(function($group) {
            return [
                'size' => $group->first()->size,
                'stock' => $group->sum('stock'),
            ];
        });

        return view('shop.show', compact('product', 'colors', 'sizes'));
    }
}