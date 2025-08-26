<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $products->transform(function ($product) {
            $product->image_url = $product->image ? asset('storage/product-Img/'.$product->image) : null;
            return $product;
        });
        
        return $products;
    }

    /**
     * Store a newly created resource in storage.
     */
        public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30|string',
            'price' => 'required|decimal:0,2 ',
            'desc' => 'required|max:60|string',
            'cat_id' => 'required|exists:categories,id',
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
            $product = new Product;
            $product->name = $request->name;
            $product->price = $request->price;
            $product->desc = $request->desc;
            $product->cat_id = $request->cat_id;

            if($request->hasFile('image')) {
                $filename = $request->file('image')->getClientOriginalName();
                $getfilenamewitoutext = pathinfo($filename, PATHINFO_FILENAME);
                $getfileExtension = $request->file('image')->getClientOriginalExtension();
                $createnewFileName = time().'_'.str_replace(' ','_', $getfilenamewitoutext).'.'.$getfileExtension;
                
                // Store in public disk (consistent path)
                $img_path = $request->file('image')->storeAs('product-Img', $createnewFileName, 'public');
                $product->image = $createnewFileName;
            }

            if($product->save()) {
                $product->image_url = asset('storage/product-Img/'.$product->image);
                
                return response()->json([
                    'status' => true,
                    'message' => 'product created successfully',
                    'data' => $product
                ], 201);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating product: '.$e->getMessage()
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);
        
        if(!$product) {
            return response()->json([
                'status' => false,
                'message' => 'product not found'
            ], 404);
        }

        $product->image_url = $product->image ? asset('storage/product-Img/'.$product->image) : null;

        return response()->json([
            'status' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|max:30|string',
            'price' => 'sometimes|required|decimal:0,2',
            'desc' => 'sometimes|required|max:60|string',
            'cat_id' => 'sometimes|required|exists:categories,id',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $product = Product::find($id);
        
        if(!$product) {
            return response()->json([
                'status' => false,
                'message' => 'product not found'
            ], 404);
        }
            $product->name = $request->name;
            $product->price = $request->price;
            $product->desc = $request->desc;
            $product->cat_id = $request->cat_id;

        if($request->hasFile('image')) {
            // Delete old image if exists
            if($product->image && Storage::disk('public')->exists('product-img/'.$product->image)) {
                Storage::disk('public')->delete('product-img/'.$product->image);
            }

            // Upload new image
            $filename = $request->file('image')->getClientOriginalName();
            $getfilenamewitoutext = pathinfo($filename, PATHINFO_FILENAME);
            $getfileExtension = $request->file('image')->getClientOriginalExtension();
            $createnewFileName = time().'_'.str_replace(' ','_', $getfilenamewitoutext).'.'.$getfileExtension;
            
            $img_path = $request->file('image')->storeAs('product-img', $createnewFileName, 'public');
            $product->image = $createnewFileName;
        }

        if($product->save()) {
            $product->image_url = $product->image ? asset('storage/product-img/'.$product->image) : null;
            
            return response()->json([
                'status' => true,
                'message' => 'product updated successfully',
                'data' => $product
            ], 200);
        }

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error updating product: '.$e->getMessage()
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        
        if(!$product) {
            return response()->json([
                'status' => false,
                'message' => 'product not found'
            ], 404);
        }

        // Delete associated image (using consistent path)
        if($product->image && Storage::disk('public')->exists('product-img/'.$product->image)) {
            Storage::disk('public')->delete('product-img/'.$product->image);
        }

        if($product->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'product deleted successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error: product not deleted'
        ], 500);
    }

}
