<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewVote;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ReviewVoteController extends Controller
{
    /**
     * Open to guests as well as logged-in users — voting Helpful/Unhelpful is
     * much lower-stakes than writing a review, and most stores let anyone do
     * it. Ownership follows the same guest-or-user pattern as Cart/Wishlist.
     */
    public function store(Request $request, ResponseService $rs)
    {
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|integer|exists:reviews,id',
            'is_helpful' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $rs->setValidationResponse($validator->errors());
        }

        $ownerCriteria = Auth::guard('web')->check()
            ? ['user_id' => Auth::guard('web')->id(), 'session_id' => null]
            : ['user_id' => null, 'session_id' => Session::getId()];

        $isHelpful = $request->boolean('is_helpful');

        $existingVote = ReviewVote::where('review_id', $request->review_id)
            ->where($ownerCriteria)
            ->first();

        // Clicking the same vote again removes it (toggle off); clicking the
        // other one switches it. Either way, only one vote per person stands.
        if ($existingVote && $existingVote->is_helpful === $isHelpful) {
            $existingVote->delete();
        } else {
            ReviewVote::updateOrCreate(
                array_merge(['review_id' => $request->review_id], $ownerCriteria),
                ['is_helpful' => $isHelpful],
            );
        }

        $review = Review::find($request->review_id);

        return $rs->setSuccessResponse('Thanks for your feedback!', [
            'helpful_count' => $review->votes()->where('is_helpful', true)->count(),
            'unhelpful_count' => $review->votes()->where('is_helpful', false)->count(),
        ]);
    }
}
