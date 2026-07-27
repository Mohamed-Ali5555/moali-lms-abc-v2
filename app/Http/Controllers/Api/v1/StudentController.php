<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment_history;
use Illuminate\Http\Request;
use App\Models\InvoceItems;
use Modules\Theme\App\Http\Services\FawryPay;
use Modules\Theme\App\Http\Services\PaymobService;
use Modules\Wallet\App\Models\WalletLog;    
use Illuminate\Support\Facades\DB;
class StudentController extends Controller
{
    public function myCourses(Request $request)
    {
        // $perPage = $request->input('per_page', 6);

        $courses = Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->join('users', 'courses.user_id', '=', 'users.id')
            ->where('enrollments.user_id', $request->user()->id)
            ->where('courses.status', 'active')
            ->whereRaw('enrollments.id = (SELECT MAX(e.id) FROM enrollments e WHERE e.user_id = enrollments.user_id AND e.course_id = enrollments.course_id)')
            ->select(
                'enrollments.*',
                'courses.slug',
                'courses.title',
                'courses.thumbnail',
                'courses.discount_flag',
                'courses.price',
                'courses.discount_price',
                'courses.created_at',
                'courses.is_paid',
                'users.name as user_name',
                'users.photo as user_photo'
            )
            ->get();

        return response()->json([
            'status' => true,
            'courses' => $courses,
        ], 200);
    }

    public function myBooks(Request $request)
    {
        $books = Payment_history::where('user_id', $request->user()->id)
            ->with(['items' => function ($query) {
                $query->where('productable_type', 'Modules\BookStore\App\Models\Book')
                    ->whereHas('item', function ($q) {
                        $q->where('status', 1);
                    });
            }])
            ->get()
            ->pluck('items')
            ->flatten()
            ->groupBy('productable_id')
            ->map(function ($items) {
                return [
                    'book' => $items->first()->item,
                    'count' => $items->sum('qty'),
                ];
            })
            ->values();
    
        if ($books->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No books found.',
                'books' => [],
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Books retrieved successfully.',
            'books' => $books,
        ], 200);
    }


    public function mypurchaseHistory(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $payments = Payment_history::with('items.item')
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'payments' => $payments,
        ], 200);
    }



    public function myinvoice(Request $request, $id)
    {
        if (!is_numeric($id) || $id < 1) {
            return response()->json([
                'status' => false,
                'message' => 'معرف الفاتورة غير صالح',
            ], 422);
        }

        $reportHistory = Payment_history::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$reportHistory) {
            return response()->json([
                'status' => false,
                'message' => 'الفاتورة غير موجودة',
            ], 404);
        }

        $invoiceItems = InvoceItems::with('item')
            ->where('payment_history_id', $id)
            ->get();

        return response()->json([
            'status' => true,
            'invoice' => $reportHistory,
            'invoice_items' => $invoiceItems,
        ], 200);
    }
    public function mywallet(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        return response()->json([
            'user_wallets' => WalletLog::with('added')
                ->where('student_id', $request->user()->id)
                ->orderBy('id', 'DESC')
                ->get(),
            // 'payment_gateways' => DB::table('payment_gateways')
            //     ->where('status', 1)
            //     ->where('identifier', '!=', 'Wallet')
            //     ->get(),
        ], 200);
    }
}
