<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        $products->transform(function($prod){
            $prod->image = url($prod->image);
            return $prod;
        });

        return response()->json($products, 200);
    }

    public function store(Request $request)
    {
        $valid = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $valid['image'] = 'images/products/' . $imageName;
        }

        $product = Product::create($valid);
        $product->image = url($product->image);

        return response()->json($product, 201);
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        $product->image = url($product->image);
        return response()->json($product, 200);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $valid = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $valid['image'] = 'images/products/' . $imageName;
        }

        $product->update($valid);
        $product->image = url($product->image);

        return response()->json($product, 200);
    }

    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(['message' => 'تم حذف المنتج بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // خصم stock بعد عملية شراء
    public function reduceStock(Request $request, string $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($id);

        if ($product->stock < $request->quantity) {
            return response()->json(['error' => 'الكمية المطلوبة تفوق المخزون الحالي'], 400);
        }

        $product->stock -= $request->quantity;
        $product->save();

        return response()->json([
            'message' => 'تم خصم الكمية بنجاح',
            'product' => $product
        ], 200);
    }

    // زيادة stock (إضافة مخزون جديد)
    public function increaseStock(Request $request, string $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($id);

        $product->stock += $request->quantity;
        $product->save();

        return response()->json([
            'message' => 'تم زيادة المخزون بنجاح',
            'product' => $product
        ], 200);
    }
}
