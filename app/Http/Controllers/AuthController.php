<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
//Sign Up
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:users',

            'password' => 'required|string|min:6',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }
  try {

            // إضافة role مباشرة في جدول users
            $user = User::create([
                'name' => $request->name,

                'password' => Hash::make($request->password),

            ]);

            return response()->json([
                'message' => 'تم تسجيل المستخدم بنجاح',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تسجيل المستخدم.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

// LogIn
public function login(Request $request)
    {
        // التحقق من صحة المدخلات
        $validator = Validator::make($request->all(), [

            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }
        // البحث عن المستخدم باستخدام البريد الإلكتروني
        $user = User::where('name', $request->name)->first();
        // التحقق من كلمة المرور
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
            ], 401);
        }

        // إنشاء التوكن
        $token = $user->createToken('auth_token')->plainTextToken;

        // إرسال التوكن والدور مع الاستجابة
        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'user' => $user,        // إرسال بيانات المستخدم (اختياري)
        ], 200);
    }

//LogOut
    public function logout(Request $request)
    {
        try {
            // chek tokens
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'لم يتم العثور على المستخدم'
                ], 401); // Unauthorized
            }

            // delete tokens
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'message' => 'تم تسجيل الخروج بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء محاولة تسجيل الخروج',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    //update informtion user personal
    public function updateUser(Request $request, $user_id)
    {

        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255|unique:users',


            'password'       => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // تجميع بيانات التحديث
        $data = $request->only([
            'name',



        ]);
        // إذا تم إدخال كلمة سر جديدة
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // تحديث البيانات في جدول users
        $user->update($data);

        // إرجاع الاستجابة مع بيانات المستخدم
        return response()->json([
            'message' => 'تم تعديل بيانات المستخدم بنجاح',
            'user'    => $user->fresh(),
        ], 200);
    }

//delet account user
    public function deleteUser($user_id)
    {
            // Search for user
            $user = User::find($user_id);

            //verify the user's presence
            if (!$user) {
                return response()->json([
                    'message' => 'المستخدم غير موجود',
                ], 404); // Status code 404: Not Found
            }


            $user->delete();

            return response()->json([
                'message' => 'تم حذف المستخدم بنجاح',
            ], 200); // Status code 200: OK
    }
}
