<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment_history;
use App\Models\User;
use App\Models\InvoceItems;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\BookStore\App\Models\Book;

class PurchaseController extends Controller
{
    public function purchase_history()
    {
        // $page_data['payments'] = Payment_history::join('courses', 'payment_histories.course_id', 'courses.id')
        //     ->join('users', 'payment_histories.user_id', 'users.id')
        //     ->where('payment_histories.user_id', auth()->user()->id)
        //     ->select('payment_histories.*', 'courses.title as course_title', 'users.name as user_name')
        //     ->latest('id')->paginate(10);


        $userId = auth()->user()->id;

        $page_data['payments'] = Payment_history::with('items.item')
            ->where('user_id', $userId)
            ->latest('id')
            ->paginate(10);

        $page_data['stats'] = [
            'total' => Payment_history::where('user_id', $userId)->count(),
            'paid' => Payment_history::where('user_id', $userId)->where('status', 'paid')->count(),
            'pending' => Payment_history::where('user_id', $userId)->where('status', '!=', 'paid')->count(),
            'amount' => round((float) Payment_history::where('user_id', $userId)->where('status', 'paid')->sum('amount'), 2),
        ];

        return view('theme::student.purchase_history.index', $page_data);

    }

    public function invoice($id)
    {
        // validate course id
        if (!is_numeric($id) && $id < 1) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        // check existence
        // $payment = Payment_history::join('courses', 'payment_histories.course_id', 'courses.id')
        //     ->join('users', 'payment_histories.user_id', 'users.id')
        //     ->where('payment_histories.id', $id)
        //     ->select('payment_histories.*', 'courses.title as course_title', 'users.name as user_name')->first();

        $report_historys= Payment_history::where('id', $id)->first();

        $invoice_items = InvoceItems::with('item')->where('payment_history_id', $id)->get();


        if (!$invoice_items) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        $page_data['invoice_items'] = $invoice_items;
        $page_data['report_historys'] = $report_historys;



        return view('theme::student.purchase_history.invoice',$page_data);

    }

    public function purchase_course($course_id)
    {

        // validate course id
        if (!is_numeric($course_id) && $course_id < 1) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        // check personal course
        if (Course::where('id', $course_id)->where('user_id', auth()->user()->id)->exists()) {
            Session::flash('error', get_phrase('Ops! You own this course.'));
            return redirect()->back();
        }

        // Check if the course is purchased and not expired
        $existingEnrollment = Enrollment::where('user_id', auth()->user()->id)
            ->where('course_id', $course_id)
            ->where(function ($query) {
                $query->where('expiry_date', '>', now()->timestamp)
                    ->orWhereNull('expiry_date');
            })
            ->exists();

        if ($existingEnrollment) {
            Session::flash('error', get_phrase('You already enrolled in this course'));
            return redirect()->back();
        }

        // get course details by id
        $course_details = Course::where('id', $course_id)->first();

        // if course doesn't exist redirect back
        if (!$course_details) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        // if course is free then enroll user and redirect to my courses
        if ($course_details->is_paid == 0) {
            $enrollment['user_id']         = auth()->user()->id;
            $enrollment['course_id']       = $course_id;
            $enrollment['enrollment_type'] = 'free';
            $enrollment['entry_date']      = time();

            $course_details = get_course_info($course_id);

            if ($course_details->expiry_period > 0) {
                $days = $course_details->expiry_period * 30;
                $enrollment['expiry_date'] = strtotime("+" . $days . " days");
            } else {
                $enrollment['expiry_date'] = null;
            }

            Enrollment::insert($enrollment);
            return redirect()->route('my.courses');
        } else {
            $query = CartItem::where('course_id', $course_id)->where('user_id', auth()->user()->id);
            if ($query->count() == 0) {
                CartItem::insert(['user_id' => auth()->user()->id, 'course_id' => $course_id, 'created_at' => date('Y-m-d H:i:s')]);
                return redirect(route('cart'));
            } elseif ($query->count() == 1) {
                return redirect(route('cart'));
            }
        }

        // redirect to cart store
        return redirect()->back();
    }

    public function payout(Request $request)
    {
        // get all item details by its id
        // return $request;
        $items_id = json_decode($request->items, true) ?: [];
        $books_id = json_decode($request->books, true) ?: [];

        $courses = $items_id;
        $books = $books_id;

        // If order is gift then select gifted user id by phone
        $giftPhone = trim((string) ($request->gifted_user_phone ?? ''));
        if ($request->boolean('is_gift') || $giftPhone !== '') {
            if ($giftPhone === '') {
                Session::flash('error', get_phrase('أدخل رقم هاتف المستلم.'));
                return redirect()->back();
            }

            $digits = preg_replace('/\D+/', '', $giftPhone) ?: '';
            if (strlen($digits) < 10 || strlen($digits) > 14) {
                Session::flash('error', get_phrase('رقم الهاتف يجب أن يكون بين 10 و 14 رقمًا.'));
                return redirect()->back();
            }

            $giftedUser = $this->findUserByPhone($giftPhone);
            if (!$giftedUser || $giftedUser->role === 'admin') {
                Session::flash('error', get_phrase('لا يوجد مستخدم بهذا الرقم.'));
                return redirect()->back();
            }

            if ((int) $giftedUser->id === (int) auth()->id()) {
                Session::flash('error', get_phrase('لا يمكنك إرسال هدية لنفسك.'));
                return redirect()->back();
            }

            $gifted_user_id = $giftedUser->id;

            $courses = [];
            foreach ($items_id as $item) {
                if (Enrollment::where('course_id', $item['id'])->where('user_id', $gifted_user_id)->doesntExist()) {
                    $courses[] = $item;
                }
            }

            if (count($items_id) > 0 && count($courses) == 0) {
                Session::flash('error', get_phrase('المستخدم مشترك بالفعل في هذه الكورسات.'));
                return redirect()->back();
            }
        }

        $course_ids = collect($courses)->pluck('id')->toArray();
        $book_ids   = collect($books)->pluck('id')->toArray();

        $selected_courses = Course::whereIn('id', $course_ids)->get();
        $selected_books   = Book::whereIn('id', $book_ids)->get();

        $items = [];
        // Prepare courses
        foreach ($selected_courses as $course) {
            $course_data = collect($courses)->firstWhere('id', $course->id);
            $items[] = [
                'id'             => $course->id,
                'title'          => $course->title,
                'subtitle'       => '',
                'price'          => $course->price,
                'discount_price' => $course->discount_flag ? $course->discount_price : 0,
                'type'           => 'course',
                'qty'            => $course_data['qty'] ?? 1,
            ];
        }

        // Prepare books
        foreach ($selected_books as $book) {
            $book_data = collect($books)->firstWhere('id', $book->id);
            $items[] = [
                'id'             => $book->id,
                'title'          => $book->title,
                'subtitle'       => '',
                'price'          => $book->price,
                'if_discount'    => $book->if_discount ?? 0,
                'discount_price' => $book->if_discount ? $book->discount_price : 0,
                'type'           => 'book',
                'qty'            => $book_data['qty'] ?? 1,
            ];
        }


        // Determine item type
        $hasCourses = count($selected_courses) > 0;
        $hasBooks = count($selected_books) > 0;

        if (!$hasCourses && !$hasBooks) {
            Session::flash('error', 'لا يوجد عناصر في السلة.');
            return redirect()->back();
        }

        $itemType = $hasCourses && $hasBooks ? 'mixed' : ($hasCourses ? 'course' : 'book');

        // Prepare payment details
        $payment_details = [
            'items' => $items,
            'custom_field' => [
                'item_type'       => $itemType,
                'pay_for'         => 'purchase',
                'user_id'         => auth()->id(),
                'user_name'       => auth()->user()->name,
                'user_email'      => auth()->user()->email,
                'user_phone'      => auth()->user()->phone,
                'user_photo'      => auth()->user()->photo,
                'cart_id'         => $items_id,
                'book_id'         => $books_id,
                'coupon_discount' => $request->coupon_discount,
                'gifted_user_id'  => $gifted_user_id ?? '',
            ],
            'success_method' => [
                'model_name'    => 'PurchaseCourse',
                'function_name' => 'purchase_course',
            ],
            'tax'            => round($request->tax, 2),
            'coupon'         => $request->coupon_code,
            'payable_amount' => round($request->payable, 2),
            'total_amount'   => round($request->payable + ($request->coupon_discount != '' ? $request->coupon_discount : 0), 2),
            'cancel_url'     => route('cart'),
            'success_url'    => route('payment.success', ''),
        ];

        Session::put(['payment_details' => $payment_details]);

        return redirect(url('/payment'));
    }

    protected function findUserByPhone(string $phone): ?User
    {
        $raw = trim($phone);
        $digits = preg_replace('/\D+/', '', $raw) ?: '';

        $candidates = array_values(array_unique(array_filter([
            $raw,
            $digits,
            ltrim($digits, '0'),
            $digits !== '' ? ('0' . ltrim($digits, '0')) : null,
            str_starts_with($digits, '20') ? ('0' . substr($digits, 2)) : null,
            str_starts_with($digits, '20') ? substr($digits, 2) : null,
            str_starts_with($digits, '0') ? ('20' . substr($digits, 1)) : null,
        ])));

        if ($candidates === []) {
            return null;
        }

        return User::query()
            ->where('role', '!=', 'admin')
            ->whereIn('phone', $candidates)
            ->first();
    }
}
