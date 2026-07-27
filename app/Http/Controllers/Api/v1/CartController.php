<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $cartItems = CartItem::join('courses', 'cart_items.course_id', '=', 'courses.id')
            ->select('cart_items.id as cart_id', 'courses.*', 'courses.id as course_id')
            ->where('cart_items.user_id', $user->id)
            ->get();

        $cartItemsBooks = CartItem::join('books', 'cart_items.book_id', '=', 'books.id')
            ->select('cart_items.id as cart_id', 'cart_items.qty as qty', 'books.*', 'books.id as book_id')
            ->where('cart_items.user_id', $user->id)
            ->get();

        $discount = 0;
        $coupon = null;

        if ($request->has('coupon')) {
            $code = $request->query('coupon');
            $coupon = Coupon::where('code', $code)->where('type', 'discount')->first();

            if (!$coupon) {
                return response()->json([
                    'status' => false,
                    'message' => 'الكوبون غير صالح.',
                ], 422);
            }

            $totalPrice = $cartItems->sum(function ($item) {
                return $item->discount_price > 0 ? $item->discount_price : $item->price;
            }) + $cartItemsBooks->sum(function ($item) {
                return $item->discount_price > 0 ? $item->discount_price : $item->price;
            });

            [$isValid, $message] = $coupon->validateForDiscount($user->id, $totalPrice);

            if (!$isValid) {
                return response()->json([
                    'status' => false,
                    'message' => $message,
                ], 422);
            }

            $discount = $coupon->calculateDiscount($totalPrice);
        }

        return response()->json([
            'status' => true,
            'cart_items' => $cartItems,
            'cart_items_books' => $cartItemsBooks,
            'discount' => $discount,
            'coupon' => $coupon,
        ], 200);
    }

    public function store(Request $request, $id)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكنك إضافة المنتج لأنك لست طالباً',
            ], 403);
        }

        $type = $request->type;

        if ($type === 'course') {
            if (Course::where('id', $id)->where('user_id', $user->id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'أنت مالك هذا الكورس بالفعل',
                ], 422);
            }

            $existingEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $id)
                ->where(function ($query) {
                    $query->where('expiry_date', '>', now()->timestamp)
                        ->orWhereNull('expiry_date');
                })->exists();

            if ($existingEnrollment) {
                return response()->json([
                    'status' => false,
                    'message' => 'لقد اشتريت هذا الكورس بالفعل',
                ], 422);
            }

            if (CartItem::where('user_id', $user->id)->where('course_id', $id)->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'تمت إضافة هذا الكورس إلى العربة من قبل',
                ], 422);
            }

            CartItem::insert([
                'user_id' => $user->id,
                'course_id' => $id,
                'type' => $type,
                'book_id' => null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'تمت إضافة الكورس بنجاح',
            ], 200);
        }

        if ($type === 'book') {
            if (CartItem::where('user_id', $user->id)->where('book_id', $id)->doesntExist()) {
                CartItem::insert([
                    'user_id' => $user->id,
                    'book_id' => $id,
                    'type' => $type,
                    'qty' => 1,
                    'course_id' => null,
                ]);

                return response()->json([
                    'status' => true,
                    'action' => 'added',
                    'message' => 'تمت إضافة هذا الكتاب بنجاح',
                ], 200);
            }

            CartItem::where('user_id', $user->id)->where('book_id', $id)->increment('qty');

            return response()->json([
                'status' => true,
                'action' => 'incremented',
                'message' => 'تمت إضافة الكتاب مرة أخرى بنجاح',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'نوع المنتج غير صالح',
        ], 422);
    }

    public function delete(Request $request, $id)
    {
        $type = $request->query('type');

        if ($type === 'book') {
            $query = CartItem::where('book_id', $id)->where('user_id', $request->user()->id)->orderBy('id', 'desc')->first();
            if ($query) {
                $query->delete();
            }
        } else {
            $query = CartItem::where('course_id', $id)->where('user_id', $request->user()->id);
            if ($query->exists()) {
                $query->delete();
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'العنصر غير موجود',
                ], 404);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'تمت إزالة العنصر من العربة',
        ], 200);
    }

    public function updateQuantity(Request $request, $cartId)
    {
        $cartItem = CartItem::where('id', $cartId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'status' => false,
                'message' => 'العنصر غير موجود',
            ], 404);
        }

        if ($request->action === 'increase') {
            $cartItem->qty++;
        } else {
            $cartItem->qty--;
            if ($cartItem->qty <= 0) {
                $cartItem->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'تمت إزالة العنصر من العربة',
                    'qty' => 0,
                ], 200);
            }
        }

        $cartItem->save();

        return response()->json([
            'status' => true,
            'qty' => $cartItem->qty,
        ], 200);
    }
}
