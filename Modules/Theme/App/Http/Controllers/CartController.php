<?php

namespace Modules\Theme\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\CouponLog;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CartController extends Controller
{


    public function index(Request $request)
    {
        if(auth()->user() != null){
            // cart items (course)
            $page_data['cart_items'] = CartItem::join('courses', 'cart_items.course_id', '=', 'courses.id')
                ->select('cart_items.id as cart_id', 'courses.*', 'courses.id as course_id')
                ->where('cart_items.user_id', auth()->user()->id)->get();

            // cart item (books)
            $page_data['cart_items_books'] = CartItem::join('books', 'cart_items.book_id', '=', 'books.id')
                ->select('cart_items.id as cart_id','cart_items.qty as qty', 'books.*', 'books.id as book_id')
                ->where('cart_items.user_id', auth()->user()->id)->get();

            $user = auth()->user()->id;
            if (request()->has('coupon')) {
                $code   = request()->query('coupon');
                $coupon = Coupon::where('code', $code)->where('type','discount')->first();
                if (!$coupon) {
                    Session::flash('error', get_phrase('الكوبون غير صالح.'));
                    return redirect()->back();
                }

                $total_price = $page_data['cart_items']->sum(function ($item) {
                    return $item->discount_price > 0 ? $item->discount_price :$item->price ;
                }) + $page_data['cart_items_books']->sum(function ($item) {
                    return $item->discount_price > 0 ? $item->discount_price :$item->price ;
                });

                [$isValid, $message] = $coupon->validateForDiscount($user, $total_price);

                if (!$isValid) {
                    Session::flash('error', $message);
                    return redirect()->back();
                }

                $discount = $coupon->calculateDiscount($total_price);
                $page_data['coupon'] = $coupon;
            }

            // cart items by course id


            //return $page_data['cart_items_books'];

            $page_data['discount'] = isset($discount) ? $discount : 0;
            // $page_data['total_course_price'] = isset($total_course_price) ? $total_course_price : 0;
            return view('theme::cart.index',$page_data);
        }else{
            return redirect()->route('theme.login')->with('error','you should login');
        }


        // $view_path = 'frontend.' . get_frontend_settings('theme') . '.student.cart.index';
        // return view($view_path, $page_data);
    }
    
    public function store(Request $request,$id)
    {

        if (!auth()->check()) {
            return response()->json([
                'result'  => false,
                'message' => 'يجب ان تسجل دخول اولا'
            ], 401);
        }

        if (auth()->user()->role == "admin") {
            Session::flash('error', get_phrase('لا يمكنك اضافه المنتج لانك لست طالب'));
            return response()->json([
                'result' => false,
                'message' => 'لا يمكنك اضافه المنتج لانك لست طالب'
            ]);
        }

            $type = $request->type;
            if($type == "course")
            {
                if (Course::where('id', $id)->where('user_id', auth()->user()->id)->exists()) {
                    Session::flash('error', get_phrase('Ops! You own this course.'));
                    return redirect()->back();
                }
                $existingEnrollment = Enrollment::where('user_id', auth()->user()->id)
                ->where('course_id', $id)
                ->where(function ($query) {
                    $query->where('expiry_date', '>', now()->timestamp)
                        ->orWhereNull('expiry_date');
                })->exists();

                if ($existingEnrollment) {
                    Session::flash('error', get_phrase('You already purchased the course.'));
                    return redirect()->back();
                }
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
                    return response()->json([
                    'result' => true,
                    'action' => 'added',
                    'message' => get_phrase('تم اضافه هذا الكتاب بنجاح')
                ]);
                }else{
                    $payment_details = session('payment_details') ?? [];
                    CartItem::where('user_id', auth()->user()->id)->where('book_id', $id)->increment('qty');
                    if(!empty($payment_details)){
                        foreach($payment_details['items'] as $key=>$item){
                            if($item['type'] == 'book' && $item['id'] == $id){
                                $payment_details['items'][$key]['qty'] = $payment_details['items'][$key]['qty'] + 1;
                                $payment_details['payable_amount'] = $payment_details['payable_amount'] + $payment_details['items'][$key]['price'];
                                Session::put(['payment_details' => $payment_details]);
                            }
                        }
                    }
                 return response()->json([
                    'result' => true,
                    'action' => 'incremented',
                    'message' => get_phrase('تم اضافه الكتاب مره اخري بنجاح')
                ]);
                }

            }else{
                $course = CartItem::where('user_id', auth()->user()->id)->where('course_id', $id)->doesntExist();
                if ($course){
                    CartItem::insert([
                        'user_id'   => auth()->user()->id,
                        'course_id' => $id,
                        'type'      => $type,
                        'book_id'   => null
                    ]);
                return response()->json([
                    'result' => true,
                    'message' => get_phrase('تم اضافه الكورس بنجاح')
                ]);
                }else{
                return response()->json([
                    'result' => false,
                    'message' => get_phrase('تم اضافه ذالك الكورس من قبل الي العربه.')
                ]);
                }
            }

            // redirect back
            Session::flash('success', get_phrase('تم اضافه المنتج الي العربه'));
            // return redirect()->back();
            return response()->json(['result' => true,
            'type'=>$type,
            'message' => 'تم اضافه المنتج الي العربه']);



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

    public function updateQuantity(Request $request,$cartId){
        // $page_data['cart_items_books'] = CartItem::join('books', 'cart_items.book_id', '=', 'books.id')
        // ->select('cart_items.id as cart_id','cart_items.qty as qty', 'books.*', 'books.id as book_id')
        // ->where('cart_items.user_id', auth()->user()->id)->get();
        $cartItem = CartItem::where('id', $cartId)
        ->where('user_id', auth()->id())
        ->firstOrFail();

        if($request->action === 'increase'){
            $cartItem->qty++;
        }else{
            $cartItem->qty--;
        }
        $cartItem->save();

        return response()->json([
            'success'=>true,
            'qty' =>$cartItem->qty,
        ]);
    }

//     public function updateQuantity(Request $request)
// {
//     $books = session()->get('cart_books', []);
//     $courses = session()->get('cart_courses', []); // الكورسات محفوظة في سيشن تانية

//     $id = $request->id;
//     $qty = $request->qty;

//     if (isset($books[$id])) {
//         $books[$id]['qty'] = $qty;
//         session()->put('cart_books', $books);
//     }

//     // حساب إجمالي الكتب
//     $books_total = collect($books)->sum(fn($item) => $item['price'] * $item['qty']);

//     // حساب إجمالي الكورسات (ملهاش كوانتيتي)
//     $courses_total = collect($courses)->sum(fn($item) => $item['price']);

//     // المجموع الكلي
//     $count_items_price = $books_total + $courses_total;

//     // الخصم والضريبة
//     $discount = session()->get('discount', 0);
//     $coupon_discount = $count_items_price * ($discount / 100);
//     $tax = (get_settings('course_selling_tax') / 100) * ($count_items_price - $coupon_discount);
//     $payable = $count_items_price - $coupon_discount + $tax;

//     return response()->json([
//         'success' => true,
//         'qty' => $qty,
//         'sub_total' => number_format($count_items_price, 2),
//         'discount' => number_format($coupon_discount, 2),
//         'tax' => number_format($tax, 2),
//         'total' => number_format($payable, 2),
//     ]);
// }

}
