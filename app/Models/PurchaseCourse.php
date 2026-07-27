<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Payment_history;
use App\Models\Course;
use App\Models\User;
use App\Services\WaPilot\WhatsAppNotifier;
use Modules\BookStore\App\Models\Book;

class PurchaseCourse extends Model
{
    use HasFactory;

    public static function purchase_course($identifier)
    {
        $payment_details = session('payment_details');
        $revenue = [
            'admin'      => 0,
            'instructor' => 0
        ];

        if (Session::has('keys')) {
            $transaction_keys          = session('keys');
            $transaction_keys          = json_encode($transaction_keys);
            $payment['transaction_id'] = $transaction_keys;
            $remove_session_item[]     = 'keys';
        }
        if (Session::has('session_id')) {
            $transaction_keys      = session('session_id');
            $payment['session_id'] = $transaction_keys;
            $remove_session_item[] = 'session_id';
        }

        // generate invoice for payment
        $payment['invoice'] = Str::random(20);


        $invoice = new Payment_history();
        $invoice->amount         = $payment_details['payable_amount'];
        $invoice->user_id        = auth()->user()->id;
        $invoice->payment_type   = $identifier;
        $invoice->tax            = $payment_details['tax'];
        $invoice->coupon         = $payment_details['coupon'];
        $invoice->invoice        = $payment['invoice'];
        $invoice->paid           = 1;
        $invoice->transaction_id = $payment['transaction_id'] ?? null;
        $invoice->session_id     = $payment['session_id'] ?? null;
        $invoice->save();

        for ($i = 0; $i < count($payment_details['items']); $i++) {

            $price           = $payment_details['items'][$i]['price'];

            if($payment_details['items'][$i]['type'] == 'course'){
            $course_discount = $payment_details['items'][$i]['discount_price'];
            $realPrice       = $course_discount > 0 ? $course_discount : $price;
                // if (get_course_creator_id($payment_details['items'][$i]['id'])->role == 'admin') {
                //     $revenue['admin'] += $realPrice;
                // } else {
                //     $revenueInstructor     = $realPrice * (get_settings('instructor_revenue') / 100);
                //     $revenue['instructor'] += $revenueInstructor;
                //     $revenue['admin']      += $realPrice - $revenueInstructor;
                // }

                $course = Course::where('id',$payment_details['items'][$i]['id'])->first();
                // dd($course->is_paid);
                if($course){

                    $course->invoice()->attach([
                        $invoice->id => [
                            'amount' => $realPrice,
                            'qty'  =>1,

                        ],
                    ]);

                    $enroll['course_id']       = $payment_details['items'][$i]['id'];
                    $enroll['user_id']         = $payment_details['custom_field']['gifted_user_id'] ? $payment_details['custom_field']['gifted_user_id'] : auth()->user()->id;
                    $enroll['enrollment_type'] = "paid";
                    $enroll['entry_date']      = time();

                    $course_details = get_course_info($payment_details['items'][$i]['id']);

                    if ($course_details->expiry_period > 0) {
                        $days = $course_details->expiry_period * 30;
                        $enroll['expiry_date'] = strtotime("+" . $days . " days");
                    } else {
                        $enroll['expiry_date'] = null;
                    }
                    $enroll['payment_history_id'] = $invoice->id;

                    // insert a new enrollment
                    DB::table('enrollments')->insert($enroll);
                    $student = User::find($enroll['user_id']);
                    if ($student) {
                        app(WhatsAppNotifier::class)->notifyEnrollment($student, $course, $realPrice ?? null);
                    }
                }
            }elseif($payment_details['items'][$i]['type'] == 'book'){
                // dd($payment_details['items']);
                $book = Book::where('id',$payment_details['items'][$i]['id'])->first();
                if($book){
                     $itemPrice = $payment_details['items'][$i]['if_discount'] == 1
                        ? $payment_details['items'][$i]['discount_price']
                        : $payment_details['items'][$i]['price'];

                         $totalBookPrice = $itemPrice * $payment_details['items'][$i]['qty'];


                            // if ($book->creator && $book->creator->role == 'admin') {
                            //     $revenue['admin'] += $totalBookPrice;
                            // } else {
                            //     $revenueInstructor     = $totalBookPrice * (get_settings('instructor_revenue') / 100);
                            //     $revenue['instructor'] += $revenueInstructor;
                            //     $revenue['admin']      += $totalBookPrice - $revenueInstructor;
                            // }
                    $book->invoice()->attach([
                        $invoice->id => [
                            // 'amount'  => $realPrice
                            'amount'  => $itemPrice * $payment_details['items'][$i]['qty'],
                            'qty'  =>$payment_details['items'][$i]['qty']
                        ],
                    ]);
                }
            }
        }
        $firstItem = $payment_details['items'][0] ?? null;
        $itemType  = $payment_details['custom_field']['item_type']
            ?? ($firstItem['type'] ?? null);

        $creatorIsAdmin = true;
        if ($firstItem) {
            if (($firstItem['type'] ?? null) === 'book') {
                $book = Book::with('creator')->find($firstItem['id']);
                $creatorIsAdmin = ! $book || ! $book->creator || $book->creator->role === 'admin';
            } else {
                $creator = get_course_creator_id($firstItem['id'] ?? null);
                $creatorIsAdmin = ! $creator || empty($creator->role) || $creator->role === 'admin';
            }
        }

        if ($creatorIsAdmin) {
            $revenue['admin'] += $payment_details['payable_amount'];
        } else {
            $revenueInstructor     = $payment_details['payable_amount'] * (get_settings('instructor_revenue') / 100);
            $revenue['instructor'] += $revenueInstructor;
            $revenue['admin']      += $payment_details['payable_amount'] - $revenueInstructor;
        }

        $invoice->admin_revenue      = $revenue['admin'];
        $invoice->instructor_revenue = $revenue['instructor'];
        $invoice->save();

        // if payment and enroll has been done then remove items from cart
        $cart_items = $payment_details['custom_field'] ?? [];

        $cart_ids = is_array($cart_items['cart_id'] ?? null) ? $cart_items['cart_id'] : [($cart_items['cart_id'] ?? null)];
        $book_ids = is_array($cart_items['book_id'] ?? null) ? $cart_items['book_id'] : [($cart_items['book_id'] ?? null)];

        $normalizeIds = function ($items) {
            return collect($items)
                ->filter()
                ->map(function ($item) {
                    if (is_array($item)) {
                        return $item['id'] ?? null;
                    }
                    if (is_object($item)) {
                        return $item->id ?? null;
                    }
                    return $item;
                })
                ->filter()
                ->values()
                ->all();
        };

        $cart_ids_remove = $normalizeIds($cart_ids);
        $book_ids_remove = $normalizeIds($book_ids);

        if (($cart_items['item_type'] ?? null) == 'book') {
            if (! empty($book_ids_remove)) {
                DB::table('cart_items')->where('user_id', auth()->user()->id)
                    ->whereIn('book_id', $book_ids_remove)
                    ->delete();
            }
        } elseif (($cart_items['item_type'] ?? null) == 'course') {
            if (! empty($cart_ids_remove)) {
                DB::table('cart_items')->where('user_id', auth()->user()->id)
                    ->whereIn('course_id', $cart_ids_remove)
                    ->delete();
            }
        } else {
            if (! empty($cart_ids_remove)) {
                DB::table('cart_items')->where('user_id', auth()->user()->id)
                    ->whereIn('course_id', $cart_ids_remove)
                    ->delete();
            }
            if (! empty($book_ids_remove)) {
                DB::table('cart_items')->where('user_id', auth()->user()->id)
                    ->whereIn('book_id', $book_ids_remove)
                    ->delete();
            }
        }

        $remove_session_item[] = 'payment_details';
        Session::forget($remove_session_item);

        if ($itemType === 'book') {
            Session::flash('success', get_phrase('تم شراء الكتب بنجاح.'));
            return redirect()->route('theme.my.books');
        }

        Session::flash('success', get_phrase('Course enrolled successfully.'));
        return redirect()->route('theme.my.courses');
    }

    public static function addPaymentData($identifier,$uuid){
        // get payment details
        $payment_details = session('payment_details');
        $revenue = [
            'admin'      => 0,
            'instructor' => 0
        ];

        if (Session::has('keys')) {
            $transaction_keys          = session('keys');
            $transaction_keys          = json_encode($transaction_keys);
            $payment['transaction_id'] = $transaction_keys;
            $remove_session_item[]     = 'keys';
        }
        if (Session::has('session_id')) {
            $transaction_keys      = session('session_id');
            $payment['session_id'] = $transaction_keys;
            $remove_session_item[] = 'session_id';
        }

        // generate invoice for payment
        $payment['invoice'] = Str::random(20);


        $invoice = new Payment_history();
        $invoice->amount         = $payment_details['payable_amount'];
        $invoice->user_id        = auth()->user()->id;
        $invoice->payment_type   = $identifier;
        $invoice->tax            = $payment_details['tax'];
        $invoice->coupon         = $payment_details['coupon'];
        $invoice->invoice        = $payment['invoice'];
        $invoice->paid           = 0;
        $invoice->uuid           = $uuid ?? null;
        $invoice->session_id     = $payment['session_id'] ?? null;
        $invoice->save();

        for ($i = 0; $i < count($payment_details['items']); $i++) {

            $price           = $payment_details['items'][$i]['price'];
            $course_discount = $payment_details['items'][$i]['discount_price'];
            $realPrice       = $course_discount > 0 ? $course_discount : $price;
            if($payment_details['items'][$i]['type'] == 'course'){

                if (get_course_creator_id($payment_details['items'][$i]['id'])->role == 'admin') {
                    $revenue['admin'] += $realPrice;
                    // $payment['admin_revenue'] = $payment_details['payable_amount'];
                } else {
                    $revenueInstructor     = $realPrice * (get_settings('instructor_revenue') / 100);
                    $revenue['instructor'] += $revenueInstructor;
                    $revenue['admin']      += $realPrice - $revenueInstructor;
                }

                $course = Course::where('id',$payment_details['items'][$i]['id'])->first();
                if($course){
                    $course->invoice()->attach([
                        $invoice->id => [
                            'amount'  => $realPrice ,
                            'qty'     => $payment_details['items'][$i]['qty'] ?? 1
                        ],
                    ]);

                    $enroll['course_id']       = $payment_details['items'][$i]['id'];
                    $enroll['user_id']         = $payment_details['custom_field']['gifted_user_id'] ? $payment_details['custom_field']['gifted_user_id'] : auth()->user()->id;
                    $enroll['enrollment_type'] = "paid";
                    $enroll['entry_date']      = time();

                    $course_details = get_course_info($payment_details['items'][$i]['id']);

                    if ($course_details->expiry_period > 0) {
                        $days = $course_details->expiry_period * 30;
                        $enroll['expiry_date'] = strtotime("+" . $days . " days");
                    } else {
                        $enroll['expiry_date'] = null;
                    }

                    // insert a new enrollment
                    DB::table('enrollments')->insert($enroll);
                    $student = User::find($enroll['user_id']);
                    if ($student) {
                        app(WhatsAppNotifier::class)->notifyEnrollment($student, $course, $realPrice ?? null);
                    }
                }
            }elseif($payment_details['items'][$i]['type'] == 'book'){
                $book = Book::where('id',$payment_details['items'][$i]['id'])->first();
                if($book){
                    $book->invoice()->attach([
                        $invoice->id => [
                            'amount'  => $realPrice,
                            'qty'     => $payment_details['items'][$i]['qty'] ?? 1
                        ],
                    ]);
                }
            }
        }

        $invoice->admin_revenue      =  $revenue['admin'];
        $invoice->instructor_revenue =  $revenue['instructor'];
        $invoice->save();
        // if payment and enroll has been done then remove items from cart
        $cart_items = $payment_details['custom_field'];

        // Ensure 'cart_id' and 'book_id' are arrays
        $cart_ids = is_array($cart_items['cart_id']) ? $cart_items['cart_id'] : [$cart_items['cart_id']];
        $book_ids = is_array($cart_items['book_id']) ? $cart_items['book_id'] : [$cart_items['book_id']];
        $course_ids = collect($cart_ids)->pluck('id')->toArray();
        $book_ids   = collect($book_ids)->pluck('id')->toArray();

        if ($cart_items['item_type'] == 'book') {
            DB::table('cart_items')->where('user_id', auth()->user()->id)
                ->whereIn('book_id', $book_ids)
                ->delete();
        } elseif ($cart_items['item_type'] == 'course') {
            DB::table('cart_items')->where('user_id', auth()->user()->id)
                ->whereIn('course_id', $course_ids)
                ->delete();
        } else {
            DB::table('cart_items')->where('user_id', auth()->user()->id)
                ->whereIn('course_id', $course_ids)
                ->delete();

            DB::table('cart_items')->where('user_id', auth()->user()->id)
                ->whereIn('book_id', $book_ids)
                ->delete();
        }

        $remove_session_item[] = 'payment_details';
        Session::forget($remove_session_item);
        Session::flash('success', 'Course enrolled successfully.');
        return redirect()->route('purchase.history');

        return true;
        //return redirect()->route('my.courses');
    }
}
