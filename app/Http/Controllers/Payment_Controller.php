<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Payment;
use App\Models\Order;

class Payment_Controller extends Controller
{
    // ✅ Get all payments of the authenticated user
    public function index()
    {
        try {
            $payments = Payment::with('paymentWay')
                ->where('user_id', auth()->id())
                ->get();

            return response()->json([
                'success' => true,
                'data'    => $payments,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch payments: ' . $e->getMessage());
            return response()->json([
                'error'   => 'Failed to fetch payments',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Create a new payment (amount comes from order total)
    public function store(Request $request)
{
    try {
        $valid = $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'payment_way_id' => 'required|exists:payment_ways,id',
            'status'         => 'required|string|in:pending,paid,failed',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $userId = auth()->id();

        \Log::info('Creating payment for user_id: ' . $userId, $valid);

        $order = \App\Models\Order::where('user_id', $userId)->findOrFail($valid['order_id']);

        // Use order total directly
        $valid['user_id'] = $userId;
        $valid['amount']  = $order->total;

        \Log::info('Final data to insert:', $valid);

        $payment = \App\Models\Payment::create($valid);

        \Log::info('Payment created:', ['id' => $payment->id]);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully',
            'data'    => $payment->load('paymentWay'),
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Payment creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'error' => 'Failed to create payment',
            'details' => $e->getMessage()
        ], 500);
    }

    \Log::info('Valid input:', $valid);
\Log::info('User ID:', ['user_id' => $userId]);
\Log::info('Order found:', $order->toArray());

}


    // ✅ Show a single payment of the authenticated user
    public function show(string $id)
    {
        try {
            $payment = Payment::with('paymentWay')
                ->where('user_id', auth()->id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $payment,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Payment not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to fetch payment',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Update payment details (amount can still be updated manually if needed)
    public function update(Request $request, string $id)
    {
        try {
            $payment = Payment::where('user_id', auth()->id())->findOrFail($id);

            $valid = $request->validate([
                'amount'         => 'sometimes|numeric|min:0',
                'status'         => 'sometimes|string|in:pending,paid,failed',
                'transaction_id' => 'nullable|string|max:255',
            ]);

            $payment->update($valid);

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data'    => $payment,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'error'   => 'Validation error',
                'details' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Payment not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to update payment',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // ✅ Delete a payment
    public function destroy(string $id)
    {
        try {
            $payment = Payment::where('user_id', auth()->id())->findOrFail($id);
            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Payment not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to delete payment',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
