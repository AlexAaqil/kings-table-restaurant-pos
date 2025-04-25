<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::withCount([
            'products',
            'products as visible_products_count' => function ($query) {
                $query->where('is_visible', 1);
            }
        ])->with(['products' => function ($query) {
            $query->orderBy('product_order')->orderBy('name');
        }])->orderBy('name')->get();

        $count_products = $categories->sum('products_count');
        $count_visible_products = $categories->sum('visible_products_count');
        $count_categories = $categories->count();

        return view('admin.products.products.index', compact(
            'categories', 
            'count_products', 
            'count_visible_products', 
            'count_categories'
        ));
    }

    public function create(Request $request)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $selected_category_id = $request->input('category_id');

        return view('admin.products.products.create', compact('categories', 'selected_category_id'));
    }

    public function store(Request $request)
    {
        $validated_data = $this->validateProductData($request);
        $validated_data['slug'] = Str::slug($validated_data['name']);
        $validated_data['is_visible'] = $request->has('is_visible');

        $images = $request->file('images', []);

        if (count($images) > self::MAX_IMAGES_PER_PRODUCT) {
            return redirect()->back()
                ->withErrors(['images' => 'You can only upload up to ' . self::MAX_IMAGES_PER_PRODUCT . ' images.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $product = Product::create($validated_data);
            
            if (!empty($images)) {
                $this->storeProductImages($images, $product);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product has been added.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($slug)
    {
        $product = Product::with('images', 'category')
            ->where([
                ['is_visible', 1],
                ['slug', $slug],
            ])->firstOrFail();

        $related_products = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(5)
            ->get();

        return view('shop.product-details', compact('product', 'related_products'));
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $product_images = $product->images()->orderBy('image_order')->get();

        return view('admin.products.products.edit', compact(
            'categories', 
            'product', 
            'product_images'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $validated_data = $this->validateProductData($request, $product);
        $validated_data['slug'] = Str::slug($validated_data['name']);
        $validated_data['is_visible'] = $request->has('is_visible');

        $images = $request->file('images', []);
        $current_image_count = $product->images()->count();

        if (($current_image_count + count($images)) > self::MAX_IMAGES_PER_PRODUCT) {
            return redirect()->route('products.edit', $product->id)
                ->withErrors(['images' => 'You can only have a maximum of ' . self::MAX_IMAGES_PER_PRODUCT . ' images per product.'])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $product->update($validated_data);

            if (!empty($images)) {
                $this->storeProductImages($images, $product);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product has been updated.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
            $image_paths = $product->images->pluck('image')->toArray();

            $product->images()->delete();
            $product->delete();

            // Delete images from storage
            foreach ($image_paths as $image_path) {
                Storage::disk('public')->delete($image_path);
            }

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', 'Product has been deleted.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->validate(['query' => 'required|string'])['query'];

        $products = Product::with('category')
            ->where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->get()
            ->each->calculateDiscount();

        return view('products.search-results', compact('products', 'query'));
    }

    public function categorizedProducts($category_slug)
    {
        $categories = ProductCategory::orderBy('name', 'asc')->get();
        $category = ProductCategory::where('slug', $category_slug)->firstOrFail();
        
        $products = $category->products()
            ->get()
            ->each->calculateDiscount();

        return view('shop.categorized-products', compact(
            'category', 
            'categories', 
            'products'
        ));
    }

    public function shop()
    {
        $categories = ProductCategory::withCount([
            'products' => function ($query) {
                $query->where('is_visible', 1);
            }
        ])->with([
            'products' => function ($query) {
                $query->where('is_visible', 1)
                    ->orderBy('product_order')
                    ->orderBy('name');
            }
        ])->orderBy('name')->get();

        $count_products = $categories->sum('products_count');

        return view('sales.shop', compact('categories', 'count_products'));
    }

    /**
     * Validate product data from request
     */
    protected function validateProductData(Request $request, Product $product = null)
    {
        $rules = [
            'name' => 'required|string|max:120|unique:products,name' . ($product ? ',' . $product->id : ''),
            'product_code' => 'nullable|numeric',
            'category_id' => 'nullable|exists:product_categories,id',
            'stock_count' => 'required|numeric',
            'safety_stock' => 'required|numeric',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'product_measurement' => 'nullable|numeric',
            'measurement_id' => 'nullable|numeric',
            'product_order' => 'nullable|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ];

        return $request->validate($rules);
    }
}
