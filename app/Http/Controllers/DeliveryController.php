<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => Delivery::with('order')->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'order_id'        => 'required|exists:orders,id',
            'status'          => 'nullable|in:pending,approved,confirmed,rejected,cancelled',
            'tracking_number' => 'nullable|string',
        ]);

        // إذا لم يُحدد status، نجعله pending
        $valid['status'] = $valid['status'] ?? 'pending';

        $delivery = Delivery::create($valid);

        // تحديث حالة الطلب المرتبط بناءً على حالة التوصيل إذا الطلب ما زال pending
        $order = $delivery->order;
        if ($order && $order->status === 'pending') {
            if ($delivery->status === 'confirmed') {
                $order->status = 'shipping';
            } else if (in_array($delivery->status, ['rejected', 'cancelled'])) {
                $order->status = 'cancelled';
            }
            $order->save();
        }

        return response()->json($delivery, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $delivery = Delivery::with('order')->findOrFail($id);
        return response()->json($delivery, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $delivery = Delivery::findOrFail($id);

        $valid = $request->validate([
            'status' => 'sometimes|in:pending,approved,confirmed,rejected,cancelled',
            'tracking_number' => 'sometimes|string',
        ]);

        $delivery->update($valid);

        // تحديث حالة الطلب المرتبط بناءً على حالة التوصيل إذا الطلب ما زال pending
        $order = $delivery->order;

    if ($order && isset($valid['status'])) {
        if ($valid['status'] === 'confirmed') {
            $order->status = 'shipping';
        } else if (in_array($valid['status'], ['rejected', 'cancelled'])) {
            $order->status = 'cancelled';
        }
        $order->save();
    }


        return response()->json($delivery, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->delete();

        return response()->json(['message' => 'تم حذف التوصيل بنجاح'], 200);
    }
}
