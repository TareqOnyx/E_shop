<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\PaymentWay;

class Payment_Way_Controller extends Controller
{
    // عرض كل طرق الدفع
    public function index()
    {
        try {
            return response()->json(PaymentWay::all(), 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في جلب طرق الدفع',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // إنشاء طريقة دفع جديدة
    public function store(Request $request)
    {
        try {
            $valid = $request->validate([
                'name'   => 'required|string|max:40|unique:payment_ways,name',
                'desc'   => 'required|string|max:40',
                'status' => 'boolean',
            ]);

            $paymentWay = PaymentWay::create($valid);

            return response()->json($paymentWay, 201);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'خطأ في التحقق من البيانات',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في إنشاء طريقة الدفع',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // عرض طريقة دفع معينة
    public function show(string $id)
    {
        try {
            $paymentWay = PaymentWay::findOrFail($id);
            return response()->json($paymentWay, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'طريقة الدفع غير موجودة'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في جلب طريقة الدفع',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // تعديل طريقة دفع
    public function update(Request $request, string $id)
    {
        try {
            $paymentWay = PaymentWay::findOrFail($id);

            $valid = $request->validate([
                'name'   => 'required|string|max:255',
                'desc'   => 'nullable|string|max:255',
                'status' => 'boolean',
            ]);

            $paymentWay->update($valid);

            return response()->json($paymentWay, 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'خطأ في التحقق من البيانات',
                'details' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'طريقة الدفع غير موجودة'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في تعديل طريقة الدفع',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // حذف طريقة دفع
    public function destroy(string $id)
    {
        try {
            $paymentWay = PaymentWay::findOrFail($id);
            $paymentWay->delete();

            return response()->json(['message' => 'تم حذف طريقة الدفع بنجاح'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'طريقة الدفع غير موجودة'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فشل في حذف طريقة الدفع',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
