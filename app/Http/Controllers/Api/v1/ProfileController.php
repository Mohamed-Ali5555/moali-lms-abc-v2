<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\FileUploader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $user->photo = get_photo('user_image', $user->photo);

        return response()->json([
            'status' => true,
            'user' => $user,
            'categories' => get_student_grade_categories(),
        ], 200);
    }

    public function update(Request $request, $user_id)
    {
        if ((int) $user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بتعديل هذا الملف الشخصي.',
            ], 403);
        }

        $rules = [
            'name' => 'required',
            'email' => (is_email_required() ? 'required' : 'nullable') . '|email|unique:users,email,' . $user_id,
            'national_id' => iqama_validation_rules((int) $user_id, is_national_id_required()),
            'category' => student_grade_category_rule(),
            'goverment' => 'required',
            'gender' => 'required',
            'phone' => array_merge(saudi_phone_validation_rules()),
            'old_password' => 'required_with:new_password',
            'new_password' => 'nullable',
            'national_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
        ];

        $user = User::findOrFail($user_id);
        if (is_national_image_required() && empty($user->national_image) && !$request->hasFile('national_image')) {
            $rules['national_image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'];
        }

        $validator = Validator::make($request->all(), $rules, array_merge(
            iqama_validation_messages(),
            saudi_phone_validation_messages()
        ));

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'email' => $request->filled('email') ? $request->email : null,
            'phone' => $request->phone,
            'national_id' => $request->filled('national_id') ? $request->national_id : null,
            'category' => $request->category,
            'goverment' => $request->goverment,
            'address' => $request->address,
            'gender' => $request->gender,
        ];

        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'كلمة المرور القديمة غير صحيحة',
                ], 422);
            }
            $data['password'] = Hash::make($request->new_password);
        }

        if ($request->hasFile('national_image')) {
            if (!empty($user->national_image) && file_exists(public_path($user->national_image))) {
                remove_file($user->national_image);
            }
            $path = 'uploads/user-national_image/' . nice_file_name($request->name, $request->national_image->extension());
            FileUploader::upload($request->national_image, $path, 400, null, 200, 200);
            $data['national_image'] = $path;
        }

        if ($request->hasFile('photo')) {
            remove_file($user->photo);
            $path = 'uploads/users/student/' . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $path, 400, null, 200, 200);
            $data['photo'] = $path;
        }

        User::where('id', $user_id)->update($data);

        $updatedUser = User::find($user_id);
        $updatedUser->photo = get_photo('user_image', $updatedUser->photo);

        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => $updatedUser,
        ], 200);
    }

    // public function updateProfilePicture(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'photo' => 'required|image|mimes:jpeg,png,jpg,webp,tiff|max:3072',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'فشل التحقق من البيانات',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $file = $request->photo;
    //     $file_name = Str::random(20) . '.' . $file->extension();
    //     $path = 'uploads/users/' . $request->user()->role . '/' . $file_name;
    //     FileUploader::upload($file, $path, null, null, 300);

    //     User::where('id', $request->user()->id)->update(['photo' => $path]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'تم تحديث صورة الملف الشخصي',
    //         'photo' => get_photo('user_image', $path),
    //     ], 200);
    // }
}
