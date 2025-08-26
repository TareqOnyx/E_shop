<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\review;

class Review_Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return review::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'product_id'  => 'required|exists:products,id',
            'review_text' => 'required|string',
            'rating'      => 'required|integer|min:1|max:5',
        ]);

        $review = Review::create($valid);

        return response()->json($review, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return review::find($id);
        return review::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::findOrFail($id);

        $valid = $request->validate([
            'review_text' => 'required|string',
            'rating'      => 'required|integer|min:1|max:5',
        ]);

        $review->update($valid);

        return response()->json($review, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         try {
            $reviewdelete = Review::findOrFail($id);
            $reviewdelete->delete();
            return response()->json(['message' => 'تم حذف المراجعة بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    }

