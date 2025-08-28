<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class Product_Controller extends Controller
{
    public function index()
    {
        try {
            $products = Product::all();

            if ($products->isEmpty()) {
                return response()->json(['message' => 'لا يوجد منتجات'], 200);
            }

            $products->transform(function ($prod) {
                $prod->image = url($prod->image);
                return $prod;
            });

            return response()->json($products, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'خطأ في جلب المنتجات', 'details' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
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

            return response()->json(['message' => 'تم إضافة المنتج بنجاح', 'product' => $product], 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'فشل في إضافة المنتج', 'details' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->image = url($product->image);

            return response()->json($product, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'المنتج غير موجود'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'فشل في جلب المنتج', 'details' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);

            $valid = $request->validate([
                'name'        => 'sometimes|string|max:255',
                'description' => 'sometimes|string|max:255',
                'price'       => 'sometimes|numeric|min:0',
                'stock'       => 'sometimes|integer|min:0',
                'image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'category_id' => 'sometimes|exists:categories,id',
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $imageName);
                $valid['image'] = 'images/products/' . $imageName;
            }

            $product->update($valid);
            $product->image = url($product->image);

            return response()->json(['message' => 'تم تحديث المنتج بنجاح', 'product' => $product], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'المنتج غير موجود'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'فشل في تحديث المنتج', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(['message' => 'تم حذف المنتج بنجاح'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'المنتج غير موجود'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'فشل في حذف المنتج', 'details' => $e->getMessage()], 500);
        }
    }

    public function reduceStock(Request $request, string $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($id);

            if ($product->stock < $request->quantity) {
                return response()->json(['error' => 'الكمية المطلوبة تفوق المخزون الحالي'], 400);
            }

            $product->stock -= $request->quantity;
            $product->save();

            return response()->json(['message' => 'تم خصم الكمية بنجاح', 'product' => $product], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'المنتج غير موجود'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'فشل في خصم المخزون', 'details' => $e->getMessage()], 500);
        }
    }

    public function increaseStock(Request $request, string $id)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($id);
            $product->stock += $request->quantity;
            $product->save();

            return response()->json(['message' => 'تم زيادة المخزون بنجاح', 'product' => $product], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'المنتج غير موجود'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'فشل في زيادة المخزون', 'details' => $e->getMessage()], 500);
        }
    }
}
