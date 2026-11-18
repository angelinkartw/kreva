<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\RatingService;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    public function products(Request $request)
    {
        return Product::with('store:id,name')->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))->latest('id')->cursorPaginate(30);
    }

    public function setProductStatus(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', 'in:active,banned,draft'], 'note' => ['nullable', 'string', 'max:300']]);
        $product = Product::findOrFail($id);
        $product->forceFill(['status' => $data['status']])->save();
        AuditLogger::log('admin.product_'.$data['status'], $product, ['note' => $data['note'] ?? null]);

        return $product;
    }

    public function reviews(Request $request)
    {
        return Review::when($request->query('status'), fn ($q, $s) => $q->where('moderation_status', $s))->latest('id')->cursorPaginate(30);
    }

    public function setReviewStatus(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', 'in:published,hidden']]);
        $review = Review::findOrFail($id);
        $review->forceFill(['moderation_status' => $data['status']])->save();
        RatingService::recalculate($review->product_id, $review->store_id);
        AuditLogger::log('admin.review_'.$data['status'], $review);

        return $review;
    }

    public function reports(Request $request)
    {
        return Report::where('status', $request->query('status', 'open'))->latest('id')->cursorPaginate(30);
    }

    public function closeReport(Request $request, int $id)
    {
        $report = Report::findOrFail($id);
        $report->update(['status' => 'closed']);

        return $report;
    }

    public function setUserStatus(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', 'in:active,suspended']]);
        $user = User::findOrFail($id);
        abort_if($user->isAdmin(), 422, 'Akun admin tidak dapat dinonaktifkan dari sini.');
        $user->forceFill(['status' => $data['status']])->save();
        if ($data['status'] === 'suspended') {
            $user->tokens()->delete();
        }
        AuditLogger::log('admin.user_'.$data['status'], $user);

        return $user;
    }
}
