<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CkeditorController extends Controller
{
    /**
     * رفع صورة من CKEditor
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        // التحقق من وجود الملف
        if (!$request->hasFile('upload')) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'لم يتم العثور على ملف للرفع'
                ]
            ], 400);
        }

        $file = $request->file('upload');

        // التحقق من صحة الملف
        if (!$file->isValid()) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'الملف غير صالح'
                ]
            ], 400);
        }

        // التحقق من نوع الملف (صور فقط)
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'نوع الملف غير مدعوم. يُسمح بالصور فقط (JPEG, PNG, GIF, WEBP)'
                ]
            ], 400);
        }

        // التحقق من حجم الملف (حد أقصى 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $maxSize) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'حجم الملف كبير جداً. الحد الأقصى 5MB'
                ]
            ], 400);
        }

        try {
            // إنشاء اسم فريد للملف
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::slug($originalName) . '_' . time() . '_' . Str::random(8) . '.' . $extension;

            // المسار النهائي في public/uploads/questions
            $directory = public_path('uploads/questions');
            
            // إنشاء المجلد إذا لم يكن موجوداً
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // حفظ الملف
            $file->move($directory, $fileName);

            // إنشاء URL عام للصورة
            $url = asset('uploads/questions/' . $fileName);

            // إرجاع الاستجابة بصيغة CKEditor
            return response()->json([
                'uploaded' => 1,
                'fileName' => $fileName,
                'url' => $url
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}

