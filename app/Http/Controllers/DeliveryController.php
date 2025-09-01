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

    // تحديث التوصيل أولاً
    $delivery->update($valid);

    // جلب الطلب المرتبط
    $order = $delivery->order;

    Log::info('Delivery update attempt', [
        'delivery_id' => $delivery->id,
        'delivery_status' => $valid['status'],
        'order_id' => $order?->id,
        'order_status_before' => $order?->status
    ]);

    if ($order && isset($valid['status'])) {
        switch ($valid['status']) {
            case 'confirmed':
                $order->status = 'shipping';
                break;
            case 'rejected':
            case 'cancelled':
                $order->status = 'cancelled';
                break;
            case 'pending':
                $order->status = 'pending';
                break;
            // يمكنك إضافة approved إذا تريد تحديث الطلب حسب هذا
        }
        $order->save();

        Log::info('Order status updated', [
            'order_id' => $order->id,
            'order_status_after' => $order->status
        ]);
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
