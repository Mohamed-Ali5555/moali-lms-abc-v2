<?php

namespace Modules\BookStore\App\Http\Controllers;
use Modules\BookStore\App\Http\Services\BookStoreService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Modules\BookStore\App\Models\Book;
use Modules\BookStore\App\Http\Requests\BookStoreRequest;
use Illuminate\Support\Facades\Session;

class BookStoreController extends Controller
{
    protected $service;

    public function __construct(BookStoreService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $page_data['categories'] = Category::where('parent_id', 0)->orderBy('sort', 'asc')->get();
        $page_data['books']      = Book::orderBy('sort', 'asc')->get();
        return view('bookstore::index',$page_data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function activation($id)
    {
        $this->service->activation($id);
        return redirect()->route('admin.bookstore')->with('success', 'book activation successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookStoreRequest $request)
    {
        $this->service->create($request);
        return redirect()->route('admin.bookstore')->with('success', 'book created successfully.');
    }
    public function book_sort(Request $request)
    {
        $books = json_decode($request->itemJSON);
        foreach ($books as $key => $value) {
            $updater = $key + 1;
            Book::where('id', $value)->update(['sort' => $updater]);
        }

        Session::flash('success', get_phrase('Books sorted successfully'));
    }
    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('bookstore::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('bookstore::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookStoreRequest $request, $id)
    {
        $this->service->update($request,$id);
        return redirect()->route('admin.bookstore')->with('success', 'book update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $this->service->destroy($id);
        return redirect()->route('admin.bookstore')->with('success', 'book destroy successfully.');
    }
}
