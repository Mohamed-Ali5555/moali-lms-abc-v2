<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment_history;

class MyBooksController extends Controller
{
    public function index()
    {
           $books = Payment_history::where('user_id',auth()->user()->id)->with(['items' => function ($query) {
                $query->where('productable_type', 'Modules\BookStore\App\Models\Book')
                    ->whereHas('item', function ($q) { // item her because inside function items that get from invoice items
                        $q->where('status', 1);
              });
            }])->get()
            ->pluck('items')->flatten()
            ->groupBy('productable_id')
            ->map(function($items){
                return[
                    'book' => $items->first()->item,
                    'count' =>$items->sum('qty'),
                ];
            });

            // dd( $books);

            // $books = Payment_history::where('user_id', auth()->id())
            // ->with(['items' => function ($query) {
            //     $query->where('productable_type', 'Modules\BookStore\App\Models\Book');
            // }])
            // ->get()
            // ->pluck('items') // نجيب فقط الـ items
            // ->flatten()      // نخليها collection واحدة
            // ->groupBy('productable_id') // نجمع حسب الكتاب
            // ->map(function ($items) {
            //     return [
            //         'book' => $items->first()->productable, // الكتاب نفسه
            //         'count' => $items->count(), // عدد مرات الشراء
            //     ];
            // });


            //  return( $page_data['reports']);

            return view('theme::student.my_books.index',compact('books'));


    }
}
