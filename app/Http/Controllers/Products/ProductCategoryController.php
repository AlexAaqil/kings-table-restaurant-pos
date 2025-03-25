<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $product_categories = ProductCategory::orderBy('name')->get();
        $count_product_categories = $product_categories->count();

        return view('admin.products.product-categories.index', compact('count_product_categories', 'product_categories'));
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'name' => 'required|string|max:80|unique:product_categories,name',
        ]);

        $validated_data['slug'] = Str::slug($validated_data['name']);

        ProductCategory::create($validated_data);

        return redirect()->route('product-categories.index')->with('success', 'Category has been added.');
    }

    public function edit(Request $request, ProductCategory $product_category)
    {
        return view('admin.products.product-categories.edit', compact('product_category'));
    }

    public function update(Request $request, ProductCategory $product_category)
    {
        $validated_data = $request->validate([
            'name' => 'required|string|max:80|unique:product_categories,name,'.$product_category->id,
        ]);

        $validated_data['slug'] = Str::slug(Str::lower($validated_data['name']));

        $product_category->update($validated_data);

        return redirect()->route('product-categories.index')->with('success', 'Category has been updated.');
    }

    public function destroy(ProductCategory $product_category)
    {
        $product_category->delete();

        return redirect()->route('product-categories.index')->with('success', 'Category has been deleted.');
    }
}
