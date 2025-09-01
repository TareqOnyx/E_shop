<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class OrderController extends Controller
{
    // ✅ جلب الطلبات الخاصة بالمستخدم
    public function index()
    {
        try {
            $orders = Order::with('items.product', 'payment', 'delivery')
                ->where('user_id', auth()->id())
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'تم جلب الطلبات',
                'data'    => $orders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'فشل في جلب الطلبات',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // ✅ إنشاء طلب جديد
public function store(Request $request)
{
    try {
        // Validate only delivery_way_id
        $valid = $request->validate([
            'delivery_way_id' => 'required|exists:delivery_ways,id',
            'status'  => 'pending',
        ]);

        // جلب السلة للمستخدم الحالي
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'السلة فارغة'], 400);
        }

        // تحقق من المخزون + احسب الإجمالي
        $total = 0;
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                return response()->json([
                    'error' => "المنتج {$item->product->name} لا يحتوي على الكمية المطلوبة"
                ], 400);
            }
            $total += $item->product->price * $item->quantity;
        }

        // إنشاء الطلب مع status تلقائي pending
        $order = Order::create([
            'user_id' => auth()->id(),
            'total'   => $total,
            'status'  => 'pending',
            'delivery_way_id' => $valid['delivery_way_id'],
        ]);

        // تحديث المخزون وإنشاء OrderItem لكل عنصر
        foreach ($cartItems as $item) {
            $product = $item->product;
            $product->stock -= $item->quantity;
            $product->save();

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $item->quantity,
                'price'      => $product->price,
            ]);
        }

        // مسح السلة
        Cart::where('user_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الطلب بنجاح',
            'data'    => $order->load('items.product')
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error'   => 'خطأ في التحقق من البيانات',
            'details' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'error'   => 'فشل في إنشاء الطلب',
            'details' => $e->getMessage()
        ], 500);
    }
}



    // ✅ تحديث حالة الطلب
    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $valid = $request->validate([
                'status' => 'required|string|in:pending,confirmed,shipped,delivered,canceled'
            ]);

            $order->update($valid);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب',
                'data'    => $order
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error'   => 'خطأ في التحقق من البيانات',
                'details' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'الطلب غير موجود'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'فشل في تحديث حالة الطلب',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
