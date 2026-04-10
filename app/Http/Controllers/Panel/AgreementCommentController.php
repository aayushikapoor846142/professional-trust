<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalAgreementComment;
use App\Models\ProfessionalAssociateAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AgreementCommentController extends Controller
{
    /**
     * Store a new comment.
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'agreement_id' => 'required|exists:professional_associate_agreements,id',
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:professional_agreement_comments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = ProfessionalAgreementComment::create([
                'agreement_id' => $request->agreement_id,
                'user_id' => Auth::id(),
                'parent_id' => $request->parent_id,
                'comment' => $request->comment,
                'status' => 'active'
            ]);

            // Load relationships
            $comment->load('user', 'replies.user');
            

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment = ProfessionalAgreementComment::findOrFail($id);

            // Check if user owns the comment
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to edit this comment'
                ], 403);
            }

            $comment->update([
                'comment' => $request->comment
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'comment' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a comment.
     */
    public function destroy($id)
    {
        try {
            $comment = ProfessionalAgreementComment::findOrFail($id);

            // Check if user owns the comment
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this comment'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments for an agreement.
     */
    public function getComments($agreementId)
    {
        try {
            $agreement = ProfessionalAssociateAgreement::findOrFail($agreementId);
            $comments = $agreement->comments()->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'comments' => $comments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

        /**
     * Get comments for an agreement as rendered HTML view.
     */
    public function getCommentsView($agreementId)
    {
        try {
            $agreement = ProfessionalAssociateAgreement::findOrFail($agreementId);
            
            // Get ALL comments (both main and replies) for this agreement
            $allComments = ProfessionalAgreementComment::with(['user','replies'])
                ->where('agreement_id', $agreement->id)
                ->orderBy('created_at', 'desc')
                ->get();
       
            return view('admin-panel.06-roles.associate.agreement.partials.comments-list', [
                'comments' => $allComments, // Pass all comments to the view
                'agreement' => $agreement
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single comment by ID.
     */
    public function getComment($id)
    {
        try {
            $comment = ProfessionalAgreementComment::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'comment' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
