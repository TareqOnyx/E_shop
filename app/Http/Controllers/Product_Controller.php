<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class Product_Controller extends Controller
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
            'desc'        => 'required|string|max:255',
            'price'       => 'required|string|max:255',
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
            'desc'        => 'required|string|max:255',
            'price'       => 'required|string|max:255',
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
}
