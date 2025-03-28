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

class ProductController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::with(['products' => function($query) {
            $query->orderBy('product_order')->orderBy('name');
        }])->orderBy('name')->get();

        return view('admin.products.products.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $selected_category_id = $request->input('category_id');

        return view('admin.products.products.create', compact('categories', 'selected_category_id'));
    }

    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'name'=> 'required|string|max:120|unique:products,name',
            'product_code' => 'nullable|numeric',
            'category_id' => 'nullable',
            'stock_count' => 'numeric',
            'safety_stock' => 'numeric',
            'buying_price' => 'numeric',
            'selling_price' => 'numeric',
            'discount_price' => 'numeric',
            'product_measurement' => 'nullable|numeric',
            'measurement_id' => 'nullable|numeric',
            'product_order' => 'nullable|numeric',
            'images' => 'max:2048',
            'description' => 'nullable',
        ]);

        $validated_data['slug'] = Str::slug($validated_data['name']);
        $validated_data['is_visible'] = $request->is_visible;

        $images = $request->file('images');

        if($images && count($images) > 5) {
            return redirect()->back()->withErrors(['images' => 'You can only upload a max of 5 images.']);
        }

        return DB::transaction(function () use ($validated_data, $request, $images) {
            $product = Product::create($validated_data);
            
            if($images) {
                $this->storeProductImages($images, $product);
            }

            return redirect()->route('products.index')->with('success', 'Product has been added.');
        });
    }

    public function show($slug)
    {
        $product = Product::with('images', 'category')->where([
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
        $product_images = $product->getProductImages;

        return view('admin.products.products.edit', compact('categories', 'product', 'product_images'));
    }

    public function update(Request $request, Product $product)
    {
        $validated_data = $request->validate([
            'name'=> 'required|string|max:120|unique:products,name,' . $product->id,
            'product_code' => 'numeric',
            'category_id' => 'nullable',
            'stock_count' => 'numeric',
            'safety_stock' => 'numeric',
            'buying_price' => 'numeric',
            'selling_price' => 'numeric',
            'discount_price' => 'numeric',
            'product_measurement' => 'nullable|numeric',
            'measurement_id' => 'nullable|numeric',
            'product_order' => 'nullable|numeric',
            'images.*' => 'image|max:2048',
            'description' => 'nullable',
        ]);
    
        $validated_data['slug'] = Str::slug($validated_data['name']);
        $validated_data['featured'] = $request->featured;
        $validated_data['is_visible'] = $request->is_visible;
    
        $images = $request->file('images') ?? []; // Ensure `$images` is an array

        if ($images) {
            $existing_images_count = $product->images()->count();
            $new_images_count = is_array($images) ? count($images) : 0;

            if ($existing_images_count + $new_images_count > 5) {
                return redirect()->route('products.edit', $product->id)
                    ->withErrors(['images' => 'You can only upload a maximum of five images.'])
                    ->withInput();
            }
        }
    
        return DB::transaction(function () use ($validated_data, $product, $images) {
            $product->update($validated_data);
            $this->updateProductImages($images, $product);
    
            return redirect()->route('products.index')->with('success', 'Product has been updated.');
        });
    }       

    public function destroy(Product $product)
    {
        return DB::transaction(function () use ($product) {
            $image_paths = $product->images->pluck('image')->toArray();

            $product->images()->delete();
            $product->delete();

            foreach ($image_paths as $image_path) {
                Storage::disk('public')->delete($image_path);
            }

            return redirect()->route('products.index')->with('success', 'Product has been deleted.');
        });
    }

    private function storeProductImages($images, Product $product)
    {
       foreach ($images as $image) {
            $filename = $this->generateImageFilename($image, $product->name, $product->id);
            $image_path = $image->storeAs('products', $filename, 'public');

            ProductImage::create([
                'image' => $image_path,
                'product_id' => $product->id,
            ]);
        }
    }
    
    private function updateProductImages($images, Product $product)
    {
        foreach ($product->images as $image) {
            $oldPath = $image->image;
            $newFilename = $this->generateImageFilenameFromPath($oldPath, $product->name, $product->id);
            $newPath = 'products/' . $newFilename;

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->move($oldPath, $newPath);
            }

            $image->update(['image' => $newPath]);
        }

        $existing_images_count = $product->images()->count();
        $new_images_count = is_array($images) ? count($images) : 0;

        if ($existing_images_count + $new_images_count > 5) {
            return redirect()->route('products.edit', $product->id)
                ->withErrors(['images' => 'You can only upload a maximum of five images.'])
                ->withInput();
        }

        if (!empty($images)) {
            $this->storeProductImages($images, $product);
        }
    }

    private function generateImageFilenameFromPath($oldPath, $newTitle, $productId)
    {
        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
        $slug = Str::slug($newTitle);
        $app_name = Str::slug(config("globals.app_name"));

        return "{$app_name}-{$slug}-{$productId}-" . uniqid() . ".{$extension}";
    }

    private function generateImageFilename($image, $title, $productId)
    {
        $extension = $image->getClientOriginalExtension();
        $slug = Str::slug($title);
        $app_name = Str::slug(config("globals.app_name"));
        return "{$app_name}-{$slug}-{$productId}-" . uniqid() . ".$extension";
    }

    public function deleteProductImage($id) {
        $image = ProductImage::find($id);

        $image->delete();

        Storage::disk('public')->delete($image->image);

        return redirect()->route('products.edit', $image->product_id)->with('success', 'Image has been deleted.');
    }

    public function sortProductImages(Request $request) {
        if(!empty($request->photo_id)) {
            $i = 1;
            foreach($request->photo_id as $photo_id) {
                $image = ProductImage::find($photo_id);
                $image->image_order = $i;
                $image->save();

                $i++;
            }
        }

        $json['success'] = true;
        echo json_encode($json);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::with('category')
        ->where('name', 'like', "%$query%")
        ->orWhere('description', 'like', "%$query%")
        ->get();

        foreach ($products as $product) {
            $product->calculateDiscount();
        }

        return view('products.search-results', compact('products', 'query'));
    }

    public function categorizedProducts($category_slug)
    {
        $categories = ProductCategory::orderBy('name', 'asc')->get();
        $category = ProductCategory::where('slug', $category_slug)->firstOrFail();
        $products = $category->products()->get();

        foreach ($products as $product) {
            $product->calculateDiscount();
        }

        return view('shop.categorized-products', compact('category', 'categories', 'products'));
    }

    public function shop()
    {
        $products = Product::with('category', 'images')->orderBy('product_order')->orderBy('name')->get();
        $count_products = $products->count();

        return view('shop', compact('count_products', 'products'));
    }
}
