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
        return Delivery::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'order_id' => 'required|integer',
            'delivery_way_id' => 'required|exists:delivery_ways,id',
            'status' => 'nullable|in:pending,approved,rejected', // فقط ثلاث حالات
            'tracking_number' => 'nullable|string',
        ]);

        // إذا لم يُحدد status، نجعله pending
        if (!isset($valid['status'])) {
            $valid['status'] = 'pending';
        }

        $delivery = Delivery::create($valid);

        return response()->json($delivery, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Delivery::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $delivery = Delivery::findOrFail($id);

        $valid = $request->validate([
            'status' => 'sometimes|in:pending,approved,rejected', // فقط ثلاث حالات
            'tracking_number' => 'nullable|string',
        ]);

        $delivery->update($valid);

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
