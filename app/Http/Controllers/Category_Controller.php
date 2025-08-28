<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class Category_Controller extends Controller
{
    public function index()
    {
        try {
            $categories = Category::all();

            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد أصناف متاحة حالياً',
                    'data'    => []
                ], 404);
            }

            $categories->transform(function ($cat) {
                $cat->image = url($cat->image);
                return $cat;
            });

            return response()->json([
                'success' => true,
                'message' => 'تم جلب جميع الأصناف بنجاح',
                'data'    => $categories
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الأصناف',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = $request->validate([
                'name'  => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/categories'), $imageName);
                $valid['image'] = 'images/categories/' . $imageName;
            }

            $category = Category::create($valid);
            $category->image = url($category->image);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الصنف بنجاح',
                'data'    => $category
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء الصنف',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->image = url($category->image);

            return response()->json([
                'success' => true,
                'message' => 'تم جلب الصنف بنجاح',
                'data'    => $category
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الصنف غير موجود',
                'error'   => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الصنف',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $category = Category::findOrFail($id);

            $valid = $request->validate([
                'name'  => 'sometimes|string|max:255',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/categories'), $imageName);
                $valid['image'] = 'images/categories/' . $imageName;
            }

            $category->update($valid);
            $category->image = url($category->image);

            return response()->json([
                'success' => true,
                'message' => 'تم تعديل بيانات الصنف بنجاح',
                'data'    => $category
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الصنف غير موجود',
                'error'   => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تعديل الصنف',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الصنف بنجاح',
                'data'    => null
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الصنف غير موجود',
                'error'   => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل حذف الصنف',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
