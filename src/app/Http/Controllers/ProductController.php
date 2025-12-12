<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductListResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function ListProduct(Request $request)
    {
        $search   = $request->search;
        $category = $request->category;

        $cacheKey = "product_data_" . md5($search . '_' . $category);

        $products = Cache::tags(['products'])->remember($cacheKey, 300, function () use ($search, $category) {
            return Product::query()
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('desc', 'like', "%{$search}%");
                    });
                })
                ->when($category, fn($q) => $q->where('category', $category))
                ->get();;
        });

        return ProductListResource::collection($products);
    }

    public function CreateProduct(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'desc' => 'required',
                'category' => 'required',
                'price' => 'required',
                'image' => 'required|max:1000|mimes:png,jpg,jpeg'
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'desc.required' => 'Deskripsi tidak boleh kosong',
                'category.required' => 'Kategori tidak boleh kosong',
                'price.required' => 'Harga tidak boleh kosong',
                'image.required' => 'Gambar tidak boleh kosong',
                'image.max' => 'Gambar maximal 1mb',
                'image.mimes' => 'Format gambar png, jpg, jpeg',
                'image.image'       => 'File harus berupa gambar',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user_id = Auth::id();

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = $user_id . '_' . time() . '.' . $extension;

        Storage::disk('public')->putFileAs('product/image', $file, $filename);

        Product::create([
            'name' => $request->name,
            'desc' => $request->desc,
            'category' => $request->category,
            'price' => $request->price,
            'image' => 'product/image/' . $filename,
        ]);

        return response()->json(['message' => 'Produk berhasil dibuat'], 200);
    }

    public function DetailProduct($id)
    {
        $product = Product::find($id);

        return new ProductDetailResource($product);
    }

    public function UpdateProductData($id, Request $request)
    {
        $product = Product::find($id);

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'desc' => 'required',
                'category' => 'required',
                'price' => 'required',
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'desc.required' => 'Deskripsi tidak boleh kosong',
                'category.required' => 'Kategori tidak boleh kosong',
                'price.required' => 'Harga tidak boleh kosong',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update([
            'name' => $request->name,
            'desc' => $request->desc,
            'category' => $request->category,
            'price' => $request->price,
        ]);

        return response()->json(['message' => 'Produk berhasil dirubah'], 200);
    }

    public function UpdateProductImage($id, Request $request)
    {
        $product = Product::find($id);

        $validator = Validator::make(
            $request->all(),
            [
                'image' => 'required|max:1000|mimes:png,jpg,jpeg'
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'desc.required' => 'Deskripsi tidak boleh kosong',
                'category.required' => 'Kategori tidak boleh kosong',
                'price.required' => 'Harga tidak boleh kosong',
                'image.required' => 'Gambar tidak boleh kosong',
                'image.max' => 'Gambar maximal 1mb',
                'image.mimes' => 'Format gambar png, jpg, jpeg',
                'image.image'       => 'File harus berupa gambar',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $user_id = Auth::id();
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = $user_id . '_' . time() . '.' . $extension;

        Storage::disk('public')->putFileAs('product/image', $file, $filename);

        $product->update([
            'image' => 'product/image/' . $filename,
        ]);

        return response()->json(['message' => 'Gambar Produk berhasil dirubah'], 200);
    }

    public function DeleteProduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus'], 200);
    }
}
