<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('goats')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $imagePath = 'https://images.unsplash.com/photo-1608755728617-aefab37d2edd?w=600&auto=format&fit=crop';
        if ($request->hasFile('image')) {
            $imageName = 'category_' . time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $imagePath = 'uploads/categories/' . $imageName;
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dibuat.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $imagePath = $category->image;
        if ($request->hasFile('image')) {
            // Delete old file if exists
            $oldPath = public_path($category->image);
            if (file_exists($oldPath) && !str_starts_with($category->image, 'http')) {
                unlink($oldPath);
            }

            $imageName = 'category_' . time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $imagePath = 'uploads/categories/' . $imageName;
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $oldPath = public_path($category->image);
        if (file_exists($oldPath) && !str_starts_with($category->image, 'http')) {
            unlink($oldPath);
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
