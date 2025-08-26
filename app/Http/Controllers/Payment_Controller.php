<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\payment;

class PaymentController extends Controller
{
    // ✅ عرض كل المدفوعات
    public function index()
    {
        return Payment::with('paymentWay')->get();
    }

    // ✅ إنشاء عملية دفع جديدة
    public function store(Request $request)
    {
        $valid = $request->validate([
            'user_id'        => 'nullable|integer',
            'order_id'       => 'nullable|integer',
            'payment_way_id' => 'required|exists:payment_ways,id',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'string|in:pending,paid,failed',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $payment = Payment::create($valid);

        return response()->json($payment, 201);
    }

    // ✅ عرض عملية دفع معينة
    public function show(string $id)
    {
        return Payment::with('paymentWay')->findOrFail($id);
    }

    // ✅ تعديل عملية دفع
    public function update(Request $request, string $id)
    {
        $payment = Payment::findOrFail($id);

        $valid = $request->validate([
            'amount'         => 'required|numeric|min:0',
            'status'         => 'string|in:pending,paid,failed',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $payment->update($valid);

        return response()->json($payment, 200);
    }

    // ✅ حذف عملية دفع
    public function destroy(string $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->delete();
            return response()->json(['message' => 'تم حذف عملية الدفع بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
