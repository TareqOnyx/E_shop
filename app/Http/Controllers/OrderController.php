<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product','payment','delivery')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الطلبات',
            'data' => $orders
        ]);
    }

    public function store(Request $request)
{
    // 1️⃣ التحقق من صحة البيانات
    $valid = $request->validate([
        'total' => 'required|numeric',
        'payment_method' => 'required|string',
    ]);

    // 2️⃣ جلب جميع عناصر Cart للمستخدم الحالي
    $cartItems = Cart::where('user_id', auth()->id())->get();
    if($cartItems->isEmpty()){
        return response()->json(['error' => 'السلة فارغة'], 400);
    }

    // 3️⃣ تحقق من المخزون لكل منتج
    foreach($cartItems as $item){
        if($item->quantity > $item->product->stock){
            return response()->json([
                'error' => "المنتج {$item->product->name} لا يحتوي على الكمية المطلوبة"
            ], 400);
        }
    }

    // 4️⃣ إنشاء الطلب
    $order = Order::create([
        'user_id' => auth()->id(),
        'total' => $valid['total'],
        'payment_method' => $valid['payment_method'],
        'status' => 'pending'
    ]);

    // 5️⃣ خصم المخزون لكل منتج بالـ Cart
    foreach($cartItems as $item){
        $product = $item->product;
        $product->stock -= $item->quantity;
        $product->save();

        // ربط المنتج بالطلب (لو عندك جدول order_products)
        $order->products()->attach($product->id, ['quantity' => $item->quantity]);
    }

    // 6️⃣ مسح السلة بعد الطلب
    Cart::where('user_id', auth()->id())->delete();

    // 7️⃣ إرجاع JSON
    return response()->json($order, 201);
}


    public function updateStatus(Request $request, $id)
    {
        $order = Order::where('id', $id)
                      ->where('user_id', auth()->id())
                      ->firstOrFail();

        $valid = $request->validate([
            'status' => 'sometimes|string|in:pending,confirmed,shipped,delivered,canceled'
        ]);

        $order->update($valid);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب',
            'data' => $order
        ]);
    }
}
