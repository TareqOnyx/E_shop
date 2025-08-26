<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryWay;

class DeliveryWayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return DeliveryWay::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $valid = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'estimated_days' => 'required|integer',
        ]);

        $deliveryWay = DeliveryWay::create($valid);

        return response()->json($deliveryWay, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
          return DeliveryWay::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
            $deliveryWay = DeliveryWay::findOrFail($id);

        $valid = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'estimated_days' => 'required|integer',
        ]);

        $deliveryWay->update($valid);

        return response()->json($deliveryWay, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $deliveryWay = DeliveryWay::findOrFail($id);
        $deliveryWay->delete();

        return response()->json(['message' => 'تم حذف طريقة التوصيل بنجاح'], 200);
    }
}
