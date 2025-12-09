<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create() {
        return view('admin.categories.create');
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|unique:categories']);
        Category::create($request->all());

        return redirect()->route('admin.categories.index')->with('success', 'Category Added');
    }

    public function edit(Category $category) {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category) {
        $request->validate(['name' => 'required']);
        $category->update($request->all());

        return redirect()->route('admin.categories.index')->with('success', 'Category Updated');
    }

    public function destroy(Category $category) {
        $category->delete();
        return back()->with('success', 'Category Deleted');
    }
}
