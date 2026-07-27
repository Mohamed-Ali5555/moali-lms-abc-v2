<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 6);

        $wishlist = Wishlist::join('courses', 'wishlists.course_id', '=', 'courses.id')
            ->join('users', 'courses.user_id', '=', 'users.id')
            ->select('wishlists.*', 'courses.*', 'courses.thumbnail as course_thumbnail', 'users.name as user_name', 'users.photo as users_photo')
            ->where('wishlists.user_id', $request->user()->id)
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'wishlist' => $wishlist,
        ], 200);
    }

    public function toggle(Request $request, $course_id = '')
    {
        if (!is_numeric($course_id) || $course_id < 1) {
            return response()->json([
                'status' => false,
                'message' => 'معرف الكورس غير صالح',
            ], 422);
        }

        $query = Wishlist::where('user_id', $request->user()->id)->where('course_id', $course_id);

        if ($query->exists()) {
            $query->delete();
            $toggleStatus = 'removed';
            $message = 'تمت إزالة العنصر من المفضلة';
        } else {
            Wishlist::insert([
                'user_id' => $request->user()->id,
                'course_id' => $course_id,
            ]);
            $toggleStatus = 'added';
            $message = 'تمت إضافة العنصر إلى المفضلة';
        }

        return response()->json([
            'status' => true,
            'toggleStatus' => $toggleStatus,
            'message' => $message,
        ], 200);
    }
}
