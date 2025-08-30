<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    // ✅ جلب عناصر السلة
   public function index()
{
    try {
        $carts = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($carts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'السلة فارغة'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب عناصر السلة',
            'data' => $carts
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'فشل في جلب عناصر السلة',
            'details' => $e->getMessage()
        ], 500);
    }
}


    // ✅ إضافة عنصر للسلة
   public function store(Request $request)
{
    try {
        $valid = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($valid['product_id']);

        // ✅ Check if requested quantity exceeds stock
        if ($valid['quantity'] > $product->stock) {
            return response()->json([
                'error' => "الكمية المطلوبة ({$valid['quantity']}) أكبر من المخزون المتاح ({$product->stock})"
            ], 400);
        }

        $cart = Cart::create([
            'user_id'    => auth()->id(),
            'product_id' => $valid['product_id'],
            'quantity'   => $valid['quantity'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة المنتج إلى السلة',
            'data'    => $cart->load('product')
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'error'   => 'خطأ في التحقق من البيانات',
            'details' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'error'   => 'فشل في إضافة العنصر للسلة',
            'details' => $e->getMessage()
        ], 500);
    }
}


    // ✅ تعديل عنصر بالسلة
    public function update(Request $request, $id)
    {
        try {
            $cart = Cart::where('id', $id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

            $valid = $request->validate([
                'quantity' => 'sometimes|integer|min:1',
            ]);

            $cart->update($valid);

            return response()->json([
                'success' => true,
                'message' => 'تم تعديل الكمية',
                'data' => $cart->load('product')
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'خطأ في التحقق من البيانات',
                'details' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'العنصر غير موجود في السلة'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في تعديل العنصر',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ حذف عنصر من السلة
    public function destroy($id)
    {
        try {
            $cart = Cart::where('id', $id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف العنصر من السلة',
                'data' => null
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'العنصر غير موجود في السلة'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في حذف العنصر من السلة',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
