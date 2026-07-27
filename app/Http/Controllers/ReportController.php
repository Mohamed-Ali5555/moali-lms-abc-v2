<?php

namespace App\Http\Controllers;

use App\Models\Payment_history;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\InvoceItems;

class ReportController extends Controller
{
    public function admin_revenue(Request $request)
    {
        if ($request->eDateRange) {
            $date                    = explode('-', $request->eDateRange);
            $start_date              = strtotime($date[0] . ' 00:00:00');
            $end_date                = strtotime($date[1] . ' 23:59:59');
            $page_data['start_date'] = $start_date;
            $page_data['end_date']   = $end_date;

            $page_data['reports'] = Payment_history::with('items.item')->where('admin_revenue', '!=', '')->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(10)->appends($request->query());
        } else {
            $start_date              = strtotime('first day of this month');
            $end_date                = strtotime('last day of this month');
            $page_data['start_date'] = $start_date;
            $page_data['end_date']   = $end_date;
            $page_data['reports']    = Payment_history::where('admin_revenue', '!=', '')->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))->latest('id')->paginate(10)->withQueryString();
        }

        return view('admin.report.admin_revenue', $page_data);
    }

    public function admin_revenue_filter(Request $request)
    {
        if ($request->eDateRange) {
            $date                            = explode('-', $request->eDateRange);
            $start_date                      = strtotime($date[0] . ' 00:00:00');
            $end_date                        = strtotime($date[1] . ' 23:59:59');
            $page_data['start_date']         = $start_date;
            $page_data['end_date']           = $end_date;
            $page_data['reports'] = Payment_history::where('admin_revenue', '!=', '')->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(10)->appends($request->query());
        } else {
            $start_date              = strtotime('first day of this month');
            $end_date                = strtotime('last day of this month');
            $page_data['start_date'] = $start_date;
            $page_data['end_date']   = $end_date;
            $page_data['reports']    = Payment_history::where('admin_revenue', '!=', '')->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(10)->withQueryString();
        }
        return view('admin.report.admin_revenue');
    }

    public function admin_revenue_delete($id)
    {
        $invoices = InvoceItems::where('payment_history_id',$id)->get();
        if($invoices){
                foreach($invoices as $invoice){
                    $invoice = InvoceItems::where('id',$invoice->id)->delete();
                }
        }
        Payment_history::where('id', $id)->delete();
        Session::flash('success', get_phrase('Admin revenue delete successfully'));
        return redirect()->back();
    }

    public function instructor_revenue(Request $request)
    {
        if ($request->eDateRange) {
            $date                            = explode('-', $request->eDateRange);
            $start_date                      = strtotime($date[0] . ' 00:00:00');
            $end_date                        = strtotime($date[1] . ' 23:59:59');
            $page_data['start_date']         = $start_date;
            $page_data['end_date']           = $end_date;
            $page_data['reports'] = Payment_history::with('items.item')->where('instructor_revenue', '!=', '')->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(10)->appends($request->query());
        } else {
            $start_date              = strtotime('first day of this month');
            $end_date                = strtotime('last day of this month');
            $page_data['start_date'] = $start_date;
            $page_data['end_date']   = $end_date;
            $page_data['reports']    = Payment_history::with('items.item')->where('instructor_revenue', '!=', '')->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(10);
        }
        return view('admin.report.instructor_revenue', $page_data);
    }

    public function instructor_revenue_delete($id)
    {

        Payment_history::where('id', $id)->delete();
        Session::flash('success', get_phrase('Instructor revenue delete successfully'));
        return redirect()->back();
    }

    public function purchase_history(Request $request)
    {
        if ($request->eDateRange) {
            $date                          = explode('-', $request->eDateRange);
            $start_date                    = strtotime($date[0] . ' 00:00:00');
            $end_date                      = strtotime($date[1] . ' 23:59:59');
            $page_data['start_date']       = $start_date;
            $page_data['end_date']         = $end_date;

          $page_data['reports']    = Payment_history::whereHas('items', function($query) {
            $query->where('productable_type', 'App\Models\Course');
            })
            ->with(['items' => function($query) {
                $query->where('productable_type', 'App\Models\Course');
            }])
            ->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(20)->appends($request->all());
        } else {
            $start_date              = strtotime('first day of this month');
            $end_date                = strtotime('last day of this month');
            $page_data['start_date'] = $start_date;
            $page_data['end_date']   = $end_date;
            // $page_data['reports']    = Payment_history::with('items.item')

           $page_data['reports'] = Payment_history::whereHas('items', function($query) {
            $query->where('productable_type', 'App\Models\Course');
            })
            ->with(['items' => function($query) {
                $query->where('productable_type', 'App\Models\Course');
            }])

            ->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(20)->withQueryString();
        }
        // return  $page_data;
        return view('admin.report.purchase_history', $page_data);
    }

    public function purchase_history_book(Request $request)
    {
        if ($request->eDateRange) {
            $date                          = explode('-', $request->eDateRange);
            $start_date                    = strtotime($date[0] . ' 00:00:00');
            $end_date                      = strtotime($date[1] . ' 23:59:59');
            $page_data['start_date']       = $start_date;
            $page_data['end_date']         = $end_date;
            $page_data['reports'] = Payment_history::with(['items' => function ($query) {
                $query->where('productable_type', 'Modules\BookStore\App\Models\Book');
            }])
           ->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
                ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
                ->latest('id')->paginate(20)->appends($request->all());
        } else {
            $start_date              = strtotime('first day of this month');
            $end_date                = strtotime('last day of this month');
            $page_data['start_date'] = $start_date;
            $page_data['end_date']   = $end_date;
            // $page_data['reports']    = Payment_history::with('items.item')
            // ->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
            //     ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
            //     ->latest('id')->paginate(10);

            $page_data['reports'] = Payment_history::with(['items' => function ($query) {
                $query->where('productable_type', 'Modules\BookStore\App\Models\Book');
            }])
            ->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
            ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))
            ->whereHas('items', function ($query) {
                $query->where('productable_type', 'Modules\BookStore\App\Models\Book');
            })
            ->latest('id')->paginate(20)->withQueryString();

        }
        // return $page_data['reports'];

        // return $page_data['reports'];
        // return  $page_data;
        return view('admin.report.purchase_history_books', $page_data);
    }
    public function purchase_history_invoice(Request $request, $id = '')
    {

        $type  = $request->type;
        // $page_data['report'] = Payment_history::where('id', $id)->first();
        $page_data['report_history'] = Payment_history::where('id', $id)->first();
        if($type=="course"){
            $page_data['report_invoices'] = InvoceItems::with('item')->where('productable_type','App\Models\Course')->where('payment_history_id', $id)->get();

        }else{
            $page_data['report_invoices'] = InvoceItems::with('item')->where('productable_type','Modules\BookStore\App\Models\Book')->where('payment_history_id', $id)->get();

        }

        // return $page_data['report_invoices'];
        return view('admin.report.report_invoice', $page_data);
    }
}
