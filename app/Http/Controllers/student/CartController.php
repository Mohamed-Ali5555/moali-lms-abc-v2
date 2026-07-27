<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponLog;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user()->id;
        // has any coupon then validate coupon
        if (request()->has('coupon')) {
            $code   = request()->query('coupon');
            $coupon = Coupon::where('code', $code)->first();
            if (! $coupon) {
                Session::flash('error', get_phrase('This coupon is not valid.'));
                return redirect()->back();
            }
            if($coupon->type != 'discount'){
                Session::flash('error', get_phrase('This coupon is not valid.'));
                return redirect()->back();
            }
            $start_date = carbon::parse($coupon->start_date);
            $expiry_date = carbon::parse($coupon->expiry);
            $now = carbon::now();

            if($coupon->status==0){
                Session::flash('error', get_phrase('Ops! coupon not active yet.'));
                return redirect()->back();
            }
            if ($coupon->status && ($now >= $expiry_date  || $now <= $start_date)) {
                Session::flash('error', get_phrase('Ops! coupon is expired .'));
                return redirect()->back();
            }

            // new code
            $users = json_decode($coupon->user_id, true);
            $check_user = array_search($user,$users);
                if(!in_array(0, $users)){
                    if($check_user === false){
                        Session::flash('error', get_phrase('You are not in collection for this coupon.'));
                        return redirect()->back();
                    }
            }

            if($coupon->limit <= (CouponLog::where(['user_id'=>$user,'coupon_id'=>$coupon->id])->count())){
                Session::flash('error', get_phrase('You have already used this coupon.'));
                return redirect()->back();
            }
            // end new code
            $discount = $coupon->discount;
            $page_data['coupon'] = $coupon;

            CouponLog::create([
                'user_id' =>auth()->user()->id,
                'coupon_id'=>$coupon->id,
            ]);
        }

        // cart items by course id
        // cart items (course)
        $page_data['cart_items'] = CartItem::join('courses', 'cart_items.course_id', '=', 'courses.id')
            ->select('cart_items.id as cart_id', 'courses.*', 'courses.id as course_id')
            ->where('cart_items.user_id', auth()->user()->id)->get();


        // cart item (books)
        $page_data['cart_items_books'] = CartItem::join('books', 'cart_items.book_id', '=', 'books.id')
        ->select('cart_items.id as cart_id','cart_items.qty as qty', 'books.*', 'books.id as book_id')
        ->where('cart_items.user_id', auth()->user()->id)->get();

        //return $page_data['cart_items_books'];

        $page_data['discount'] = isset($discount) ? $discount : 0;

        $view_path = 'frontend.' . get_frontend_settings('theme') . '.student.cart.index';

        return view($view_path, $page_data);
    }

    public function store(Request $request,$id)
    {
        $type = $request->type;
        // check personal course
        if (Course::where('id', $id)->where('user_id', auth()->user()->id)->exists()) {
            Session::flash('error', get_phrase('Ops! You own this course.'));
            return redirect()->back();
        }

        // Check if the course is purchased and not expired
        $existingEnrollment = Enrollment::where('user_id', auth()->user()->id)
            ->where('course_id', $id)
            ->where(function ($query) {
                $query->where('expiry_date', '>', now()->timestamp)
                    ->orWhereNull('expiry_date');
            })
            ->exists();

        if ($existingEnrollment) {
            Session::flash('error', get_phrase('You already purchased the course.'));
            return redirect()->back();
        }


        if($type=='book'){

            if (CartItem::where('user_id', auth()->user()->id)->where('book_id', $id)->doesntExist()) {
                CartItem::insert([
                    'user_id'   => auth()->user()->id,
                    'book_id'   => $id,
                    'type'      => $type,
                    'qty'       => 1,
                    'course_id' => null
                ]);
            }else{
               CartItem::where('user_id', auth()->user()->id)->where('book_id', $id)->increment('qty');

            }


            // if (in_array($data['book']->id, $courseIds)) {
            //     foreach($cart['books'] as $key=>$book){
            //         if($book['book_id'] == $data['book']->id){
            //             $cart['books'][$key]['qty']++;
            //             $cart['books'][$key]['total'] =  $cart['books'][$key]['qty'] *  $cart['books'][$key]['price'];
            //         }
            //     }
        }else{
            // if course_id doesn't exit in cart then insert course_id
            if (CartItem::where('user_id', auth()->user()->id)->where('course_id', $id)->doesntExist()) {
                CartItem::insert([
                    'user_id'   => auth()->user()->id,
                    'course_id' => $id,
                    'type'      =>$type,
                    'book_id'   => null]);
            }
        }


        // redirect back
        Session::flash('success', get_phrase('Item added to the cart.'));
        return redirect()->back();
        // return response()->json(['success' => true, 'message' => 'Item added to cart!']);

    }

    public function update(Request $request){
        $cartItem = CartItem::where('user_id', auth()->id())
        ->where('book_id', $request->id)
        ->first();

        if ($cartItem) {
        if ($request->qty > 0) {
            CartItem::where('user_id', auth()->user()->id)->where('book_id', $request->id)->decrement('qty');

            } else {
            $cartItem->delete();
            }
            }

        return response()->json(['success' => true, 'message' => 'Item decrement to cart!']);
    }
    public function delete(Request $request,$id)
    {
        $type = $request->query('type');
        if($type=='book'){
             // if user has selected item then delete item else redirect to cart page
             $query = CartItem::where('book_id', $id)->where('user_id', auth()->user()->id)->orderBy('id', 'desc')->first();
        }else{
             // if user has selected item then delete item else redirect to cart page
             $query = CartItem::where('course_id', $id)->where('user_id', auth()->user()->id);
        }


        if ($query->exists()) {
            $query->delete();
            Session::flash('success', get_phrase('Item removed from cart.'));
        } else {
            Session::flash('error', get_phrase('Data not found.'));
        }
        return redirect()->back();
    }
}
