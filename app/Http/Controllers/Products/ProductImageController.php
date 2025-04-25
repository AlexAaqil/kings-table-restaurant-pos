<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    protected function store(array $images, Product $product)
    {
        foreach ($images as $image) {
            $filename = $this->generateImageFilename($image, $product->name, $product->id);
            $image_path = $image->storeAs('products', $filename, 'public');

            ProductImage::create([
                'image' => $image_path,
                'product_id' => $product->id,
                'image_order' => $product->images()->count() + 1,
            ]);
        }
    }

    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);

        DB::beginTransaction();

        try {
            $product_id = $image->product_id;
            Storage::disk('public')->delete($image->image);
            $image->delete();

            DB::commit();

            return redirect()->route('products.edit', $product_id)
                ->with('success', 'Image has been deleted.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }

    protected function generateImageFilename($image, string $title, int $productId): string
    {
        $extension = $image->getClientOriginalExtension();
        $slug = Str::slug($title);
        $app_name = Str::slug(config("globals.app_name"));
        
        return sprintf(
            "%s-%s-%d-%s.%s",
            $app_name,
            $slug,
            $productId,
            uniqid(),
            $extension
        );
    }

    public function sort(Request $request)
    {
        $request->validate([
            'photo_id' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->photo_id as $index => $photo_id) {
                ProductImage::where('id', $photo_id)
                    ->update(['image_order' => $index + 1]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
