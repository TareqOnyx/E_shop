<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب عناصر السلة',
            'data' => $carts
        ]);
    }
public function store(Request $request)
{
    // 1️⃣ التحقق من صحة البيانات
    $valid = $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
    ]);

    // 2️⃣ جلب المنتج للتحقق من المخزون
    $product = Product::findOrFail($valid['product_id']);

    // 3️⃣ تحقق من المخزون
    if($valid['quantity'] > $product->stock){
        return response()->json(['error'=>'الكمية المطلوبة أكبر من المخزون'], 400);
    }

    // 4️⃣ إضافة المنتج إلى Cart
    $cart = Cart::create([
        'user_id' => auth()->id(),
        'product_id' => $valid['product_id'],
        'quantity' => $valid['quantity'],
    ]);

    // 5️⃣ إرجاع JSON
    return response()->json($cart, 201);
}


    public function update(Request $request, $id)
    {
        $cart = Cart::where('id', $id)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

        $valid = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart->update($valid);

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل الكمية',
            'data' => $cart->load('product')
        ]);
    }

    public function destroy($id)
    {
        $cart = Cart::where('id', $id)
                    ->where('user_id', auth()->id())
                    ->firstOrFail();

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف العنصر من السلة',
            'data' => null
        ]);
    }
}

