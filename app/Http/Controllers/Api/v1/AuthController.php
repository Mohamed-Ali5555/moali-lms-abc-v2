<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\FileUploader;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'nullable|string',
            'phone' => 'nullable|string',
            'password' => 'required|string',
        ]);

        $deviceId = $request->header('deviceId') ?? $request->deviceId;
        $email = $request->email ?? '';
        $phone = $request->phone ?? '';

        $user = User::where('email', $email)
            ->orWhere('phone', $phone)
            ->where('status', 1)
            ->first();

        if ($user && Hash::check($fields['password'], $user->password)) {
            if ($user->current_device_id && $user->current_device_id !== $deviceId) {
                return response()->json([
                    'status' => false,
                    'message' => 'أنت مسجل الدخول بالفعل من جهاز آخر.',
                ], 403);
            }

            $user->current_device_id = $deviceId;
            $user->save();

            $token = $user->createToken('auth-token')->plainTextToken;
            $user->photo = get_photo('user_image', $user->photo);

            return response()->json([
                'status' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'البيانات غير صحيحة!',
        ], 401);
    }

    public function signup(Request $request)
    {
        $nationalImageRule = is_national_image_required()
            ? ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200']
            : ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'];

        $emailRule = is_email_required()
            ? ['required', 'string', 'email', 'unique:users,email']
            : ['nullable', 'string', 'email', 'unique:users,email'];

        $nationalIdRule = is_national_id_required()
            ? ['required', 'numeric', 'digits:14', 'unique:users,national_id']
            : ['nullable', 'numeric', 'digits:14', 'unique:users,national_id'];

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => $emailRule,
            'phone' => ['required', 'numeric', 'digits_between:10,14', 'different:parent_phone'],
            'parent_phone' => ['required', 'numeric', 'digits_between:10,14'],
            'national_id' => $nationalIdRule,
            'category' => student_grade_category_rule(),
            'gender' => ['required'],
            'national_image' => $nationalImageRule,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'الاسم مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'يجب إدخال بريد إلكتروني صحيح.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل.',
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.numeric' => 'يجب أن يكون رقم الهاتف رقمًا.',
            'phone.digits_between' => 'يجب أن يكون رقم الهاتف بين 10 و 14 رقمًا.',
            'phone.different' => 'يجب أن يكون رقم الهاتف مختلفًا عن رقم ولي الأمر.',
            'parent_phone.required' => 'رقم هاتف ولي الأمر مطلوب.',
            'parent_phone.numeric' => 'يجب أن يكون رقم هاتف ولي الأمر رقمًا.',
            'parent_phone.digits_between' => 'يجب أن يكون رقم هاتف ولي الأمر بين 10 و 14 رقمًا.',
            'national_id.required' => 'الرقم القومي مطلوب.',
            'national_id.numeric' => 'يجب أن يكون الرقم القومي رقمًا.',
            'national_id.digits' => 'يجب أن يكون الرقم القومي مكونًا من 14 رقمًا.',
            'national_id.unique' => 'هذا الرقم القومي مستخدم بالفعل.',
            'category.required' => 'التصنيف مطلوب.',
            'gender.required' => 'النوع مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed' => 'يجب تأكيد كلمة المرور.',
            'national_image.required' => 'صورة البطاقة مطلوبة.',
            'national_image.image' => 'يجب أن يكون الملف صورة.',
            'national_image.mimes' => 'يجب أن تكون الصورة بصيغة: jpeg, png, jpg, webp.',
            'national_image.max' => 'أقصى حجم مسموح للصورة هو 50 ميجا.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->filled('email') ? $request->email : null,
            'phone' => $request->phone,
            'parent_phone' => $request->parent_phone,
            'national_id' => $request->filled('national_id') ? $request->national_id : null,
            'category' => $request->category,
            'goverment' => $request->goverment,
            'gender' => $request->gender,
            'role' => 'student',
            'status' => 1,
            'password' => Hash::make($request->password),
            'email_verified_at' => Carbon::now(),
        ];

        if ($request->hasFile('national_image')) {
            $userData['national_image'] = 'uploads/user-national_image/' . nice_file_name($request->name, $request->national_image->extension());
            FileUploader::upload($request->national_image, $userData['national_image'], 500, null, 200, 200);
        }

        if (get_settings('student_email_verification') != 1) {
            $userData['email_verified_at'] = Carbon::now();
        }

        $user = User::create($userData);
        event(new Registered($user));

        $token = $user->createToken('auth-token')->plainTextToken;
        $user->photo = get_photo('user_image', $user->photo);

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء المستخدم بنجاح',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->current_device_id = null;
            $user->save();
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الخروج بنجاح.',
        ], 200);
    }

    public function forgot_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return response()->json([
            'status' => $status == Password::RESET_LINK_SENT,
            'message' => $status == Password::RESET_LINK_SENT
                ? 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.'
                : 'تعذر إرسال رابط إعادة التعيين. تأكد من البريد الإلكتروني وحاول مجددًا.',
        ], $status == Password::RESET_LINK_SENT ? 200 : 422);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->photo = get_photo('user_image', $user->photo);
            return response()->json(['status' => true, 'user' => $user], 200);
        }

        return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
    }

    public function registerCategories()
    {
        return response()->json([
            'status' => true,
            'source' => student_grade_source(),
            'categories' => get_student_grade_categories(),
        ], 200);
    }
}
