<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\Category;
use App\Models\Course;
use App\Models\FileUploader;
use App\Models\User;

class CategoryController extends Controller
{
    //


    public function index()
    {
        $page_data['categories'] = Category::where('parent_id', 0)->orderBy('sort', 'asc')->get();
        return view('admin.category.index', $page_data);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|max:255',
            'parent_id'   => 'required|numeric|min:0',
            'description' => 'max:500',
            'thumbnail'   =>'required_if:parent_id,0|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'status'      => 'required|in:1,0',

        ]);

        if (Category::where('slug', slugify($request->title))->where('parent_id',$request->parent_id)->exists()) {
            return redirect(route('admin.categories'))->with('error', get_phrase('There cannot be more than one category with the same name. Please change your category name'));
        }

        $data['parent_id']   = $request->parent_id ?? 0;
        $data['title']       = $request->title;
        $data['status']      = $request->status;
        $data['slug']        = slugify($request->title);
        $data['sort']        = 0;
        $data['description'] = $request->description;
        $data['created_at']  = date('Y-m-d H:i:s');
        $data['updated_at']  = date('Y-m-d H:i:s');

        if(isset($request->thumbnail)){
            $data['thumbnail'] = "uploads/category-thumbnail/" . nice_file_name($request->title, $request->thumbnail->extension());
            FileUploader::upload($request->thumbnail, $data['thumbnail'], 500, null, 200, 200);
        }


        Category::insert($data);

        return redirect(route('admin.categories'))->with('success', get_phrase('Category added successfully'));
    }

    public function edit()
    {
    }

    public function update(Request $request, $id)
    {
        $query = Category::where('id', $id);
        $pre_data = Category::where('id', $id)->first();

        $validated = $request->validate([
            'title'       => 'required|max:255',
            'parent_id'   => 'required|numeric|min:0',
            'description' => 'max:500',
            'status'      => 'required|in:1,0',

        ]);

        if (Category::where('slug', slugify($request->title))->where('parent_id',$request->parent_id)->where('id', '!=', $id)->count() > 0) {
            return redirect(route('admin.categories'))->with('error', get_phrase('There cannot be more than one category with the same name. Please change your category name'));
        }

        $data['parent_id']    = $request->parent_id;
        $data['title']        = $request->title;
        $data['slug']         = slugify($request->title);
        $data['description']  = $request->description;
        $data['updated_at']   = date('Y-m-d H:i:s');
        $data['status']       = $request->status;

        if (isset($request->thumbnail) && $request->thumbnail != '') {
            $data['thumbnail'] = "uploads/category-thumbnail/" . nice_file_name($request->title, $request->thumbnail->extension());
            FileUploader::upload($request->thumbnail, $data['thumbnail'], 500, null, 200, 200);
            remove_file($pre_data->thumbnail);
        }

        $query->update($data);

        return redirect(route('admin.categories'))->with('success', get_phrase('Category updated successfully'));
    }

    public function delete($id)
    {
        $query = Category::where('id', $id);

        if ($query->first()->parent_id > 0) {
            remove_file($query->first()->thumbnail);
            $query->delete();
        } else {
            foreach ($query->first()->childs as $sub_category) {
                $sub_query = Category::where('id', $sub_category->id);
                remove_file($sub_query->first()->thumbnail);
                $sub_query->delete();
            }
            remove_file($query->first()->thumbnail);
            $query->delete();
        }

        return redirect(route('admin.categories'))->with('success', get_phrase('Category deleted successfully'));
    }

    public function transferPreview(Request $request)
    {
        $validated = $request->validate([
            'from_id' => 'required|integer|exists:categories,id',
            'to_id'   => 'required|integer|exists:categories,id|different:from_id',
        ]);

        $from = Category::withCount('childs')->findOrFail($validated['from_id']);
        $to   = Category::withCount('childs')->findOrFail($validated['to_id']);

        if ((int) $from->parent_id !== 0 || (int) $to->parent_id !== 0) {
            return response()->json([
                'ok' => false,
                'message' => get_phrase('يمكن النقل بين السنوات الدراسية فقط (التصنيفات الرئيسية)'),
            ], 422);
        }

        $childIds = Category::where('parent_id', $from->id)->pluck('id');

        $students = User::where('role', 'student')->where('category', $from->id)->count();
        $courses  = Course::whereIn('category_id', $childIds)->count();
        $subjects = (int) $from->childs_count;

        $books = 0;
        if (Schema::hasTable('books') && Schema::hasColumn('books', 'category_id')) {
            $books = DB::table('books')->where('category_id', $from->id)->count();
        }

        $bankCategories = 0;
        if (Schema::hasTable('bank_quizs_categories') && Schema::hasColumn('bank_quizs_categories', 'category_id')) {
            $bankCategories = DB::table('bank_quizs_categories')->where('category_id', $from->id)->count();
        } elseif (Schema::hasTable('bank_questions_categories') && Schema::hasColumn('bank_questions_categories', 'category_id')) {
            $bankCategories = DB::table('bank_questions_categories')->where('category_id', $from->id)->count();
        }

        return response()->json([
            'ok' => true,
            'from' => [
                'id' => $from->id,
                'title' => $from->title,
            ],
            'to' => [
                'id' => $to->id,
                'title' => $to->title,
            ],
            'counts' => [
                'students' => $students,
                'subjects' => $subjects,
                'courses' => $courses,
                'books' => $books,
                'bank_categories' => $bankCategories,
            ],
        ]);
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'from_id'            => 'required|integer|exists:categories,id',
            'to_id'              => 'required|integer|exists:categories,id|different:from_id',
            'transfer_students'  => 'nullable|boolean',
            'transfer_subjects'  => 'nullable|boolean',
            'transfer_books'     => 'nullable|boolean',
            'transfer_bank'      => 'nullable|boolean',
            'confirm'            => 'accepted',
        ]);

        $from = Category::findOrFail($validated['from_id']);
        $to   = Category::findOrFail($validated['to_id']);

        if ((int) $from->parent_id !== 0 || (int) $to->parent_id !== 0) {
            return redirect(route('admin.categories'))
                ->with('error', get_phrase('يمكن النقل بين السنوات الدراسية فقط (التصنيفات الرئيسية)'));
        }

        $transferStudents = $request->boolean('transfer_students');
        $transferSubjects = $request->boolean('transfer_subjects');
        $transferBooks    = $request->boolean('transfer_books');
        $transferBank     = $request->boolean('transfer_bank');

        if (!$transferStudents && !$transferSubjects && !$transferBooks && !$transferBank) {
            return redirect(route('admin.categories'))
                ->with('error', get_phrase('اختر عنصراً واحداً على الأقل للنقل'));
        }

        $summary = [
            'students' => 0,
            'subjects' => 0,
            'books' => 0,
            'bank_categories' => 0,
        ];

        try {
            DB::transaction(function () use (
                $from,
                $to,
                $transferStudents,
                $transferSubjects,
                $transferBooks,
                $transferBank,
                &$summary
            ) {
                if ($transferStudents) {
                    $summary['students'] = User::where('role', 'student')
                        ->where('category', $from->id)
                        ->update(['category' => $to->id]);
                }

                if ($transferSubjects) {
                    $summary['subjects'] = Category::where('parent_id', $from->id)
                        ->update(['parent_id' => $to->id]);
                }

                if ($transferBooks && Schema::hasTable('books') && Schema::hasColumn('books', 'category_id')) {
                    $summary['books'] = DB::table('books')
                        ->where('category_id', $from->id)
                        ->update(['category_id' => $to->id]);
                }

                if ($transferBank) {
                    if (Schema::hasTable('bank_quizs_categories') && Schema::hasColumn('bank_quizs_categories', 'category_id')) {
                        $summary['bank_categories'] = DB::table('bank_quizs_categories')
                            ->where('category_id', $from->id)
                            ->update(['category_id' => $to->id]);
                    } elseif (Schema::hasTable('bank_questions_categories') && Schema::hasColumn('bank_questions_categories', 'category_id')) {
                        $summary['bank_categories'] = DB::table('bank_questions_categories')
                            ->where('category_id', $from->id)
                            ->update(['category_id' => $to->id]);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
            return redirect(route('admin.categories'))
                ->with('error', get_phrase('حدث خطأ أثناء النقل. لم يتم تطبيق أي تغييرات.'));
        }

        $parts = [];
        if ($summary['students'] > 0) {
            $parts[] = $summary['students'] . ' ' . get_phrase('طالب');
        }
        if ($summary['subjects'] > 0) {
            $parts[] = $summary['subjects'] . ' ' . get_phrase('مادة');
        }
        if ($summary['books'] > 0) {
            $parts[] = $summary['books'] . ' ' . get_phrase('كتاب');
        }
        if ($summary['bank_categories'] > 0) {
            $parts[] = $summary['bank_categories'] . ' ' . get_phrase('تصنيف بنك أسئلة');
        }

        $message = get_phrase('تم النقل بنجاح من') . ' «' . $from->title . '» ' . get_phrase('إلى') . ' «' . $to->title . '»';
        if (!empty($parts)) {
            $message .= ' — ' . implode(' · ', $parts);
        } else {
            $message .= ' — ' . get_phrase('لا توجد بيانات مطابقة للنقل');
        }

        return redirect(route('admin.categories'))->with('success', $message);
    }
}
