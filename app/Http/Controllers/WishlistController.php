<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المفضلة',
            'data' => $items
        ]);
    }

    public function store(Request $request)
    {
        $valid = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $item = Wishlist::firstOrCreate([
            'user_id'    => auth()->id(),
            'product_id' => $valid['product_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة المنتج للمفضلة',
            'data' => $item->load('product')
        ], 201);
    }

    public function destroy($id)
    {
        $item = Wishlist::where('id', $id)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المنتج من المفضلة',
            'data' => null
        ]);
    }
}
