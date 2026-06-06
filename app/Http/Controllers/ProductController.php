<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['images', 'sizes'])->latest()->paginate(12);
        return view('product.index', compact('products'));
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'photo' => 'required|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,ico,heic,heif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/bmp,image/tiff,image/x-icon|max:20480',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'required|string|max:50',
            'sizes.*.price' => 'required|numeric|min:0',
            'sizes.*.stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,ico,heic,heif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/bmp,image/tiff,image/x-icon|max:20480',
        ]);

        DB::beginTransaction();
        try {
            // Upload main photo
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('product', 'public');
                $validated['photo'] = $photoPath;
            }

            // Create product
            $product = Product::create($validated);

            // Create sizes
            if ($request->has('sizes')) {
                foreach ($request->sizes as $sizeData) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size' => $sizeData['name'],
                        'price' => $sizeData['price'],
                        'stock' => $sizeData['stock'] ?? 0,
                    ]);
                }
            }

            // Upload and create product images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('product/images', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'photo' => $imagePath,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.product.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $product = Product::with(['images', 'sizes'])->findOrFail($id);
        return view('product.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = Product::with(['images', 'sizes'])->findOrFail($id);
        return view('product.edit', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,ico,heic,heif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/bmp,image/tiff,image/x-icon|max:20480',
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'required|string|max:50',
            'sizes.*.price' => 'required|numeric|min:0',
            'sizes.*.stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,ico,heic,heif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/bmp,image/tiff,image/x-icon|max:20480',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_image,id',
        ]);

        DB::beginTransaction();
        try {
            // Update main photo if new one uploaded
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                    Storage::disk('public')->delete($product->photo);
                }
                $validated['photo'] = $request->file('photo')->store('product', 'public');
            }

            // Update product
            $product->update($validated);

            // Update sizes - delete all and recreate
            if ($request->has('sizes')) {
                $product->sizes()->delete();
                foreach ($request->sizes as $sizeData) {
                    ProductSize::create([
                        'product_id' => $product->id,
                        'size' => $sizeData['name'],
                        'price' => $sizeData['price'],
                        'stock' => $sizeData['stock'] ?? 0,
                    ]);
                }
            }

            // Delete selected images
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = ProductImage::find($imageId);
                    if ($image && $image->product_id == $product->id) {
                        if ($image->photo && Storage::disk('public')->exists($image->photo)) {
                            Storage::disk('public')->delete($image->photo);
                        }
                        $image->delete();
                    }
                }
            }

            // Add new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('product/images', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'photo' => $imagePath,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.product.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Delete photo
        if ($product->photo && Storage::disk('public')->exists($product->photo)) {
            Storage::disk('public')->delete($product->photo);
        }

        // Delete related images
        foreach ($product->images as $image) {
            if ($image->photo && Storage::disk('public')->exists($image->photo)) {
                Storage::disk('public')->delete($image->photo);
            }
        }

        $product->delete();

        return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully.');
    }
}
