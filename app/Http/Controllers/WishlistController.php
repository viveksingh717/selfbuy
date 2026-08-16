<?php

namespace App\Http\Controllers;

use App\Services\ResponseService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    private $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index()
    {
        $wishlistItems = $this->wishlistService->getWishlistItems();

        return view('shop.wishlist', compact('wishlistItems'));
    }

    public function toggle(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:product_models,id',
            'product_attribute_id' => 'nullable|integer|exists:product_attributes,id',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? $rs->setValidationResponse($validator->errors())
                : back()->withErrors($validator);
        }

        $result = $this->wishlistService->toggle(
            (int) $request->product_id,
            $request->product_attribute_id ? (int) $request->product_attribute_id : null,
        );

        if ($request->ajax()) {
            if (!$result['status']) {
                return $rs->setErrorResponse($result['message']);
            }

            return $rs->setSuccessResponse($result['message'], [
                'action' => $result['action'],
                'wishlist_count' => $result['wishlist_count'],
                'product_id' => (int) $request->product_id,
            ]);
        }

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }

    public function destroy(Request $request, $id, ResponseService $rs)
    {
        $result = $this->wishlistService->removeItem((int) $id);

        if ($request->ajax()) {
            return $result['status']
                ? $rs->setSuccessResponse($result['message'], ['wishlist_count' => $result['wishlist_count']])
                : $rs->setErrorResponse($result['message']);
        }

        return back()->with($result['status'] ? 'success' : 'error', $result['message']);
    }
}
