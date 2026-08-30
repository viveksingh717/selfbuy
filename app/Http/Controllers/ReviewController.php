<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\Review;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function store(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:product_models,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator)->withInput();
        }

        $user = Auth::guard('web')->user();
        $data = $validator->validated();
        $product = ProductModel::find($data['product_id']);

        // Reviews are restricted to customers who actually bought the product —
        // reading reviews stays open to everyone, only writing one is gated.
        if (!$product || !$product->purchasedBy($user->id)) {
            $message = 'Only customers who have purchased this product can write a review.';

            return $request->ajax()
                ? $rs->setErrorResponse($message)
                : back()->with('error', $message);
        }

        // One review per user per product (unique constraint) — resubmitting
        // updates the existing review instead of erroring or duplicating it.
        // is_verified_purchase is always true here since the check above
        // already requires it.
        Review::updateOrCreate(
            ['product_id' => $data['product_id'], 'user_id' => $user->id],
            [
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'comment' => $data['comment'],
                'is_verified_purchase' => true,
            ],
        );

        return $request->ajax()
            ? $rs->setSuccessResponse('Thanks for your review!', [
                'average_rating' => $product->averageRating(),
                'reviews_count' => $product->reviewsCount(),
            ])
            : back()->with('success', 'Thanks for your review!');
    }
}
