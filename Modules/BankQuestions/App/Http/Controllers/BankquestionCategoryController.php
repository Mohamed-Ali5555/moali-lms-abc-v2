<?php

namespace Modules\BankQuestions\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\BankQuestions\App\Models\BankQuestionsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
class BankquestionCategoryController extends Controller
{
    public function index()
    {
        $page_data['categories'] = BankQuestionsCategory::orderBy('id','DESC')->paginate(32);
        return view('bankquestions::category.index', $page_data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'title'       => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data['title']       = $request->title;
        $data['slug']        = slugify($request->title);
        $data['category_id'] = $request->category_id;
        $data['status']      = 1;
        BankQuestionsCategory::insert($data);

        Session::flash('success', get_phrase('bank question categories has been created.'));
        return redirect()->back();
    }



    public function update($id ,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'title'       => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $question_category = BankQuestionsCategory::where('id', $id);
        if ($question_category->doesntExist()) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        $data['title']       = $request->title;
        $data['slug']        = slugify($request->title);
        $data['category_id'] = $request->category_id;
        $question_category->update($data);

        Session::flash('success', get_phrase('BankQuestionCategory has been updated.'));
        return redirect()->back();
    }


    public function show($id) //destroy
    {
        $question_category = BankQuestionsCategory::where('id', $id);
        if ($question_category->doesntExist()) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }
        $question_category->delete();

        Session::flash('success', get_phrase('BankQuestionCategory has been deleted.'));
        return redirect()->back();
    }
}
