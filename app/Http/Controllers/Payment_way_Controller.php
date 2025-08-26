<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentWay;

class PaymentWayController extends Controller
{
    //  عرض كل طرق الدفع
    public function index()
    {
        return PaymentWay::all();
    }

    //  إنشاء طريقة دفع جديدة
    public function store(Request $request)
    {
        $valid = $request->validate([
            'name'   => 'sometimes|string|max:255',
            'desc'   => 'sometimes|string|max:255',
            'status' => 'boolean',
        ]);

        $paymentWay = PaymentWay::create($valid);

        return response()->json($paymentWay, 201);
    }

    //  عرض طريقة دفع معينة
    public function show(string $id)
    {
        return PaymentWay::findOrFail($id);
    }

    //  تعديل طريقة دفع
    public function update(Request $request, string $id)
    {
        $paymentWay = PaymentWay::findOrFail($id);

        $valid = $request->validate([
            'name'   => 'required|string|max:255',
            'desc'   => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $paymentWay->update($valid);

        return response()->json($paymentWay, 200);
    }

    //  حذف طريقة دفع
    public function destroy(string $id)
    {
        try {
            $paymentWay = PaymentWay::findOrFail($id);
            $paymentWay->delete();
            return response()->json(['message' => 'تم حذف طريقة الدفع بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
