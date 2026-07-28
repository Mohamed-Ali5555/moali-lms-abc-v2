<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Payment_history;
use Illuminate\Support\Facades\Auth;
use Modules\BookStore\App\Models\Book;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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



        public function view($id)
    {
        $book = Book::where('id', $id)->where('status', 1)->firstOrFail();

        if (!$this->userOwnsBook($book->id)) {
            abort(403, 'غير مصرح لك بعرض هذا الكتاب');
        }

        if (!$book->hasReadableContent()) {
            return redirect()->route('theme.my.books')->with('error', 'لا يوجد ملف لهذا الكتاب حالياً');
        }

        return view('theme::student.my_books.view', compact('book'));
    }

    /**
     * Stream uploaded PDF securely for purchased books only.
     */
    public function file($id): BinaryFileResponse
    {
        $book = Book::where('id', $id)->where('status', 1)->firstOrFail();

        if (!$this->userOwnsBook($book->id)) {
            abort(403, 'غير مصرح لك بعرض هذا الكتاب');
        }

        if ($book->file_type !== 'file' || empty($book->file_path)) {
            abort(404);
        }

        $path = public_path($book->file_path);
        if (!is_file($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    protected function userOwnsBook(int $bookId): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Payment_history::where('user_id', Auth::id())
            ->whereHas('items', function ($query) use ($bookId) {
                $query->where('productable_type', Book::class)
                    ->where('productable_id', $bookId);
            })
            ->exists();
    }
}
