<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Delivery;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries.
     */
    public function index()
    {
        return response()->json(Delivery::all(), 200);
    }

    /**
     * Store a newly created delivery.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'nullable|in:pending,approved,rejected,cancelled',
            'tracking_number' => 'nullable|string',
        ]);

        // إذا لم يُحدد status، نجعله pending
        if (!isset($valid['status'])) {
            $valid['status'] = 'pending';
        }

        $delivery = Delivery::create($valid);

        // تحديث حالة الطلب تلقائيًا عند إنشاء التوصيل
        $order = $delivery->order;
        if ($order) {
            if ($delivery->status === 'confirmed') {
                $order->status = 'shipping';
            } else if (in_array($delivery->status, ['rejected', 'cancelled'])) {
                $order->status = 'cancelled';
            } else if ($delivery->status === 'pending') {
                $order->status = 'pending';
            }
            $order->save();
        }

        return response()->json($delivery, 201);
    }

    /**
     * Display the specified delivery.
     */
    public function show(string $id)
    {
        $delivery = Delivery::findOrFail($id);
        return response()->json($delivery, 200);
    }

    /**
     * Update the specified delivery.
     */
    public function update(Request $request, string $id)
    {
        $delivery = Delivery::findOrFail($id);

        $valid = $request->validate([
            'status' => 'sometimes|in:pending,approved,confirmed,rejected,cancelled',
            'tracking_number' => 'sometimes|string',
        ]);

        $delivery->update($valid);

        // Logging لتتبع البيانات أثناء الاختبار
        Log::info('Delivery updated', ['delivery_id' => $delivery->id, 'status' => $valid['status']]);

        // تحديث حالة الطلب المرتبط بناءً على حالة التوصيل
        $order = $delivery->order;
        if ($order && isset($valid['status'])) {
            if ($valid['status'] === 'confirmed') {
                $order->status = 'shipping';
            } else if (in_array($valid['status'], ['rejected', 'cancelled'])) {
                $order->status = 'cancelled';
            } else if ($valid['status'] === 'pending') {
                $order->status = 'pending';
            }
            $order->save();
            Log::info('Order status updated', ['order_id' => $order->id, 'status' => $order->status]);
        }

        return response()->json($delivery, 200);
    }

    /**
     * Remove the specified delivery.
     */
    public function destroy(string $id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->delete();

        return response()->json(['message' => 'تم حذف التوصيل بنجاح'], 200);
    }
}
