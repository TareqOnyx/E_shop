<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $categories->transform(function ($category) {
            $category->image_url = $category->image ? asset('storage/img/'.$category->image) : null;
            return $category;
        });
        
        return $categories;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $category = new Category;
            $category->name = $request->name;

            if($request->hasFile('image')) {
                $filename = $request->file('image')->getClientOriginalName();
                $getfilenamewitoutext = pathinfo($filename, PATHINFO_FILENAME);
                $getfileExtension = $request->file('image')->getClientOriginalExtension();
                $createnewFileName = time().'_'.str_replace(' ','_', $getfilenamewitoutext).'.'.$getfileExtension;
                
                // Store in public disk (consistent path)
                $img_path = $request->file('image')->storeAs('img', $createnewFileName, 'public');
                $category->image = $createnewFileName;
            }

            if($category->save()) {
                $category->image_url = asset('storage/img/'.$category->image);
                
                return response()->json([
                    'status' => true,
                    'message' => 'Category uploaded successfully',
                    'data' => $category
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating category: '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);
        
        if(!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->image_url = $category->image ? asset('storage/img/'.$category->image) : null;

        return response()->json([
            'status' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|max:50|string',
        'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $category = Category::find($id);
        
        if(!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $category->name = $request->name;

        if($request->hasFile('image')) {
            // Delete old image if exists
            if($category->image && Storage::disk('public')->exists('img/'.$category->image)) {
                Storage::disk('public')->delete('img/'.$category->image);
            }

            // Upload new image
            $filename = $request->file('image')->getClientOriginalName();
            $getfilenamewitoutext = pathinfo($filename, PATHINFO_FILENAME);
            $getfileExtension = $request->file('image')->getClientOriginalExtension();
            $createnewFileName = time().'_'.str_replace(' ','_', $getfilenamewitoutext).'.'.$getfileExtension;
            
            $img_path = $request->file('image')->storeAs('img', $createnewFileName, 'public');
            $category->image = $createnewFileName;
        }

        if($category->save()) {
            $category->image_url = $category->image ? asset('storage/img/'.$category->image) : null;
            
            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        }

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error updating category: '.$e->getMessage()
        ], 500);
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        
        if(!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found'
            ], 404);
        }

        // Delete associated image (using consistent path)
        if($category->image && Storage::disk('public')->exists('img/'.$category->image)) {
            Storage::disk('public')->delete('img/'.$category->image);
        }

        if($category->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error: Category not deleted'
        ], 500);
    }
}