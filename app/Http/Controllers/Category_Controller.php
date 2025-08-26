<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\category;

class Category_Controller extends Controller
{
    public function index()
    {
        $categories = category::all();

        $categories->transform(function($cat){
            $cat->image = url($cat->image);
            return $cat;
        });

        return response()->json($categories, 200);
    }

    public function store(Request $request)
    {
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

        $category = category::create($valid);
        $category->image = url($category->image);

        return response()->json($category, 201);
    }

    public function show(string $id)
    {
        $category = category::findOrFail($id);
        $category->image = url($category->image);
        return response()->json($category, 200);
    }

    public function update(Request $request, string $id)
    {
        $category = category::findOrFail($id);

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

        return response()->json($category, 200);
    }

    public function destroy(string $id)
    {
        try {
            $category = category::findOrFail($id);
            $category->delete();
            return response()->json(['message' => 'تم حذف الصنف بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
