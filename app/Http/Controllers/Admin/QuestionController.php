<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Modules\BankQuestions\App\Models\BankQuestions;
use App\Models\FileUploader;
use Illuminate\Support\Facades\File;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        //return $request;
        $validator = Validator::make($request->all(), [
            'title'          => 'required|string',
            'type'           => 'required',
            'answer'         => 'required',
            'question_image' =>'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'options_data'   => 'required_if:type,mcq',
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

        $data['quiz_id'] = $request->quiz_id;
        $data['title']   = $request->title;
        $data['type']    = $request->type;
        $data['answer']  = $answer;
        if(isset($request->question_image)){
            $folder = "uploads/questions-image/";
            $folderPath = public_path($folder);
            if (!File::isDirectory($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }
            $imageNameRandom = uniqid() . '.' . $request->question_image->extension();
            $data['question_image'] = $folder . $imageNameRandom;
            FileUploader::upload($request->question_image, $data['question_image'], 1200, null);
        }
        Question::insert($data);

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

        ]);

        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }


        $question = BankQuestions::where('id',$request->question)->first();



        $data['quiz_id'] = $request->quiz_id;
        $data['title']   = $question->title;
        $data['type']    = $question->type;
        $data['answer']  = $question->answer;
        $data['options'] = $question->options;

        Question::create($data);

        return response()->json([
            'status'       => true,
            'success'      => get_phrase('Question has been added.'),
            'functionCall' => 'responseBack()',
        ]);
    }

    public function delete($id)
    {
        $question = Question::where('id', $id)->first();
        if (! $question) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        $question->delete();
        Session::flash('success', get_phrase('Question has been deleted.'));
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $question = Question::where('id', $id)->first();
        if (! $question) {
            return response()->json([
                'error' => get_phrase('Data not found.'),
            ]);
        }
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            // 'type'    => 'required',
        ]);

        if ($validator->fails()) {
            return json_encode(array('validationError' => $validator->getMessageBag()->toArray()));
        }

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

        $data['quiz_id'] = $request->quiz_id;
        $data['title']   = $request->title;
        $data['type']    = $request->type;
        $data['answer']  = $answer;
        if(isset($request->question_image)){
            $folder = "uploads/questions-image/";
            $folderPath = public_path($folder);
            if (!File::isDirectory($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }
            $imageNameRandom = uniqid() . '.' . $request->question_image->extension();
            $data['question_image'] = $folder . $imageNameRandom;
            FileUploader::upload($request->question_image, $data['question_image'], 1200, null);
            remove_file($question->question_image);

        }
        Question::where('id', $id)->update($data);

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
            Question::where('id', $value)->update(['sort' => $updater]);
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
            $path   = "admin.questions.{$action}_{$types[$request->type]}";

            if ($request->id) {
                $page_data['question'] = Question::where('id', $request->id)->first();
            }
        }

        return view($path, $page_data);
    }
}
