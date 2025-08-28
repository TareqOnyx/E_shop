<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    // Sign Up
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'تم تسجيل المستخدم بنجاح',
                'user'    => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تسجيل المستخدم.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Log In
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token'   => $token,
            'user'    => $user,
        ], 200);
    }

    // Log Out
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    // Get Current User
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    // Update User Info
    public function updateUser(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        // تحقق أن المستخدم الحالي هو نفسه أو admin (اختياري)
        if ($request->user()->id !== (int)$user_id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255|unique:users,name,' . $user_id,
            'phone'    => 'nullable|string|max:15|unique:users,phone,' . $user_id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $request->only(['name', 'phone']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'message' => 'تم تعديل بيانات المستخدم بنجاح',
            'user'    => $user->fresh(),
        ], 200);
    }

    // Delete Account
    public function deleteUser(Request $request, $user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        // تحقق أن المستخدم الحالي هو نفسه أو admin (اختياري)
        if ($request->user()->id !== (int)$user_id) {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'تم حذف المستخدم بنجاح',
        ], 200);
    }
}
