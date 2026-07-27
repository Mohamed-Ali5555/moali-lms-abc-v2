<?php

namespace Modules\BankQuestions\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\BankQuestions\App\Models\BankQuizs;
use Illuminate\Http\Request;
use Modules\BankQuestions\App\Models\QuizeQuiestions;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Modules\BankQuestions\App\Models\BankQuestions;
use Modules\BankQuestions\App\Models\BankQuestionsCategory;
use App\Models\FileUploader;
use Illuminate\Support\Facades\File;

class QuestionController extends Controller
{
    public function index(Request $request){
        $query = BankQuestions::with('quizs');
          if(request()->filled('search')){
            $search = $request->search;
            $query->where(function($q) use($search){
                $q->where('title', 'LIKE', '%' . $search . '%')
                ->orWhereHas('category',function($query1) use($search){
                  $query1->where('title', 'LIKE', '%' . $search . '%');
                })->orWhereHas('category.category',function($query2) use($search){
                  $query2->where('title', 'LIKE', '%' . $search . '%');
                })->orWhereHas('quizs',function($query3) use($search){
                  $query3->where('title', 'LIKE', '%' . $search . '%');
                });

            });
        }

        if (isset($request->category) && $request->category != '' && $request->category != 'all') {
            if (str_starts_with($request->category, 'main_')) {
                $mainCategoryId = str_replace('main_', '', $request->category);
                $ids = BankQuestionsCategory::where('category_id', $mainCategoryId)->pluck('id')->toArray();
                $query->whereIn('category_id',$ids);
                $page_data['parent_cat'] = $mainCategoryId;
            }elseif (str_starts_with($request->category, 'sub_')) {
                $subCategoryId = str_replace('sub_', '', $request->category);
                $query->where(['category_id'=>$subCategoryId]);
                $page_data['child_cat'] = $subCategoryId;
            }
        }

          $questions= $query->orderBy('id','DESC')->paginate(20)->withQueryString();

        return view('bankquestions::questions.list', compact('questions'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string',
            'type'    => 'required',
            'answer'  => 'required',
            'options_data' => 'required_if:type,mcq',
        ], [
            'options_data.required_if' => 'When type is MCQ, options are required.',
        ]);

        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }

        $answer = null;
        if ($request->type == 'mcq') {
            $payload = json_decode($request->options_data, true);
            $options = $payload['options'] ?? [];
            $answers = $payload['answers'] ?? [];
            $images  = $payload['images'] ?? [];
            $storedImages = [];
            foreach ($images as $img) {
                $name   = $img['name'];
                $base64 = $img['base64'];

                [$type, $fileData] = explode(';', $base64);
                [, $fileData] = explode(',', $fileData);

                $fileData = base64_decode($fileData);

                $extension = explode('/', str_replace('data:', '', $type))[1] ?? 'png';
                $fileName  = uniqid() . '.' . $extension;

                $path = public_path('uploads/questions/');

                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                file_put_contents($path . $fileName, $fileData);

                $storedImages[$name] = 'uploads/questions/' . $fileName;
            }
            foreach ($options as $opt) {
                $val = $opt['value'];

                if (isset($storedImages[$val])) {
                    $data['options'][] = $storedImages[$val];
                } else {
                    $data['options'][] = $val;
                }
            }
            foreach ($answers as &$ans) {
                if (isset($storedImages[$ans])) {
                    $ans = $storedImages[$ans];
                }
            }
            $answer          = json_encode($answers);
            $data['options'] = json_encode($data['options']);
        } elseif ($request->type == 'fill_blanks') {
            $answers = json_decode($request->answer);
            $answer  = json_encode(array_column($answers, 'value'));
        } elseif ($request->type == 'true_false') {
            $answer = $request->answer;
        }
        $data['category_id'] = $request->category_id ?? null;
        $data['title']       = $request->title;
        $data['type']        = $request->type;
        $data['answer']      = $answer;

        $question = BankQuestions::create([
            'options'     => $data['options'] ?? null,
            'category_id' => $data['category_id'],
            'title'       => $data['title'],
            'type'        => $data['type'],
            'answer'      => $data['answer'],
        ]);
        if($request->quiz_id){
                $question->quizs()->sync($request->quiz_id);
        }
        // else{
        //     QuizeQuiestions::create([
        //         'quiz_id'     => $request->quiz_id,
        //         'question_id' => $question->id,
        //     ]);
        // }

        // $data['quiz_id']     = $request->quiz_id ?? null;

        return response()->json([
            'status'       => true,
            'success'      => get_phrase('تم إضافة السؤال بنجاح. يمكنك إضافة سؤال آخر.'),
            'functionCall' => 'responseBack()',
        ]);
    }

    public function choose(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'question'   => 'required',
            'quiz_id'   => 'required'
        ]);

        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }


        $question = BankQuestions::where('id',$request->question)->first();
        $quiz = BankQuizs::where('id',$request->quiz_id)->first();

        if (!$question || !$quiz) {
            return response()->json(['error' => 'Question not found.'], 404);
        }

        if(QuizeQuiestions::where('question_id',$question->id)->where('quiz_id',$quiz->id)->exists()){
            return response()->json(['error' => 'Question aleady found.'], 404);

        }
        QuizeQuiestions::create([
            'quiz_id'     => $quiz->id,
            'question_id' => $question->id,
        ]);

        return response()->json([
            'status'       => true,
            'success'      => get_phrase('Question has been added.'),
            'functionCall' => 'responseBack()',
        ]);
    }

    public function delete($quiz,$id)
    {
        $question = BankQuestions::where('id', $id)->first();
        // $quizQuestions =  QuizeQuiestions::where('id',$question->id)->first();

        if (! $question) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }
        $quizQuestion = QuizeQuiestions::where(['quiz_id'=>$quiz,'question_id'=>$id])->first();

        $quizQuestion->delete();
        Session::flash('success', get_phrase('Question has been deleted.'));
        return redirect()->back();
    }

    public function deleteQuestions($id)
    {
        $question = BankQuestions::where('id', $id)->first();

        if (! $question) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }
        $question->delete();
        $quizQuestion = QuizeQuiestions::where(['question_id'=>$id])->delete();

        $question->delete();
        Session::flash('success', get_phrase('Question has been deleted.'));
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        // dd($request->title);
        $question = BankQuestions::where('id', $id)->first();
        if (! $question) {
            return response()->json([
                'error' => get_phrase('Data not found.'),
            ]);
        }

        $validator = Validator::make($request->all(), [
            'title'   => 'required|string',
            'type'    => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }
         //    dd($request->title);
        // احتفظ بالقيم الأصلية
        $answer = $question->answer;
        $data = [
            'options' => $question->options
        ];

        // تحديث الإجابة والخيارات فقط إذا تم إرسالها
        if ($request->type == 'mcq' && $request->has('options_data') && !empty($request->options_data)) {
            $payload = json_decode($request->options_data, true);

            // إذا فشل تحليل JSON، احتفظ بالقيم الأصلية
            if (!$payload || !is_array($payload)) {
                $payload = ['options' => [], 'answers' => [], 'images' => []];
            }

            $options = $payload['options'] ?? [];
            $answers = $payload['answers'] ?? [];
            $images  = $payload['images'] ?? [];

            $storedImages = [];
            foreach ($images as $img) {
                $name   = $img['name'];
                $base64 = $img['base64'];

                [$type, $fileData] = explode(';', $base64);
                [, $fileData] = explode(',', $fileData);

                $fileData = base64_decode($fileData);

                $extension = explode('/', str_replace('data:', '', $type))[1] ?? 'png';
                $fileName  = uniqid() . '.' . $extension;

                $path = public_path('uploads/questions/');

                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                file_put_contents($path . $fileName, $fileData);

                $storedImages[$name] = 'uploads/questions/' . $fileName;
            }

            // إعادة تعيين options كـ array فارغ لتجنب التكرار
            $data['options'] = [];

            foreach ($options as $opt) {
                $val = $opt['value'];

                if (isset($storedImages[$val])) {
                    $data['options'][] = $storedImages[$val];
                } else {
                    $data['options'][] = $val;
                }
            }
            foreach ($answers as &$ans) {
                if (isset($storedImages[$ans])) {
                    $ans = $storedImages[$ans];
                }
            }
            $answer          = json_encode($answers);
            $data['options'] = json_encode($data['options']);
        } elseif ($request->type == 'fill_blanks' && $request->has('answer') && !empty($request->answer)) {
            $answers = json_decode($request->answer);
            if ($answers && is_array($answers)) {
                $answer = json_encode(array_column($answers, 'value'));
            }
        } elseif ($request->type == 'true_false' && $request->has('answer') && !empty($request->answer)) {
            $answer = $request->answer;
        }

        $data['title']       = $request->title;
        $data['type']        = $request->type;
        $data['answer']      = $answer;

        BankQuestions::where('id', $id)->update($data);

        if($request->quiz_id){

            $question->quizs()->sync($request->quiz_id);
        }else{
            $question->quizs()->detach();

        }

        return response()->json([
            'status'       => true,
            'success'      => get_phrase('Question has been updated.'),
            'functionCall' => 'responseBack()',
        ]);

    }

    public function sort(Request $request)
    {
        $question = json_decode($request->itemJSON);
        foreach ($question as $key => $value) {
            $updater = $key + 1;
            BankQuestions::where('id', $value)->update(['sort' => $updater]);
        }
        Session::flash('success', get_phrase('Questions has been sorted.'));
    }

    public function load_type(Request $request)
    {
        $page_data = [];
        $types     = [
            'mcq'         => 'mcq',
            'fill_blanks' => 'fill_blanks',
            'true_false'  => 'true_false',
        ];

        if (isset($types[$request->type])) {
            $action = $request->id ? 'edit' : 'create';
            $path   = "bankquestions::questions.{$action}_{$types[$request->type]}";

            if ($request->id) {
                $page_data['question'] = BankQuestions::where('id', $request->id)->first();
            }
        }

        return view($path, $page_data);
    }

    public function QuizesUsingCategory($id){
        $quizes = BankQuizs::where(['category_id'=>$id])->get();
        return response()->json($quizes);
    }
}
