<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\FileUploader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Hash;

class MyProfileController extends Controller
{
    public function index()
    {
        $page_data['user_details'] = User::find(auth()->user()->id);
        $page_data['categories'] = get_student_grade_categories();



        return view('theme::student.my_profile.index',$page_data);

    }

    public function update(Request $request, $user_id)
    {
        $rules = [
            'name'            => 'required',
            'email'           => (is_email_required() ? 'required' : 'nullable') . '|email|unique:users,email,' . $user_id,
            'national_id'     => iqama_validation_rules((int) $user_id, is_national_id_required()),
            'category'        => student_grade_category_rule(),
            'goverment'       => 'required',
            'gender'          =>'required',
            'phone'           => array_merge(saudi_phone_validation_rules(), ['different:parent_phone']),
            'parent_phone'    => saudi_phone_validation_rules(),
            'old_password'    => 'required_with:new_password',
            'new_password'    => 'nullable',
            'national_image'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
        ];

        $user = User::find($user_id);
        if (is_national_image_required() && empty($user?->national_image) && !$request->hasFile('national_image')) {
            $rules['national_image'] = ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'];
        }

        $validator = Validator::make($request->all(), $rules, array_merge(
            iqama_validation_messages(),
            saudi_phone_validation_messages()
        ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!$user) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Old password does not match')->withInput();
            }
            $data['password']     = Hash::make($request->new_password);
        }

        $data['name']             = $request->name;
        $data['email']            = $request->filled('email') ? $request->email : null;
        $data['phone']            = $request->phone;
        $data['national_id']      = $request->filled('national_id') ? $request->national_id : null;
        $data['category']         = $request->category;
        $data['goverment']        = $request->goverment;
        $data['parent_phone']     = $request->parent_phone;
        $data['address']          = $request->address;
        $data['phone']            = $request->phone;
        $data['gender']           = $request->gender;

        // if ($request->national_image) {
        //     $originalFileName = $user->id . '-' . $request->national_image->getClientOriginalName();
        //     $destinationPath = 'uploads/user-national_image/' . $originalFileName;
        //     // Move the file to the destination path
        //     FileUploader::upload($request->national_image, $destinationPath, 600);
        //     $data['national_image'] = $destinationPath;
        // }



        // if (isset($request->national_image) && $request->hasFile('national_image')) {
        //     remove_file(User::where('id', $user_id)->first()->national_image);
        //     $path = "uploads/user-national_image/" . nice_file_name($request->name, $request->national_image->extension());
        //     FileUploader::upload($request->national_image, $path, 400, null, 200, 200);
        //     $data['national_image'] = $path;
        // }
        if ($request->hasFile('national_image')) {
            $user = User::find($user_id);

            if (!empty($user->national_image) && file_exists(public_path($user->national_image))) {
                remove_file($user->national_image);
            }

            $path = "uploads/user-national_image/" . nice_file_name($request->name, $request->national_image->extension());
            FileUploader::upload($request->national_image, $path, 400, null, 200, 200);
            $data['national_image'] = $path;
        }


        if (isset($request->photo) && $request->hasFile('photo')) {
            remove_file(User::where('id', $user_id)->first()->photo);
            $path = "uploads/users/student/" . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $path, 400, null, 200, 200);
            $data['photo'] = $path;
        }


        User::where('id', $user_id)->update($data);
        Session::flash('success', get_phrase('Profile updated successfully.'));
        return redirect()->back();
    }

    public function update_profile_picture(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp,tiff|max:3072',
        ]);

        // process file
        $file      = $request->photo;
        $file_name = Str::random(20) . '.' . $file->extension();
        $path      = 'uploads/users/' . auth()->user()->role . '/' . $file_name;
        FileUploader::upload($file, $path, null, null, 300);

        User::where('id', auth()->user()->id)->update(['photo' => $path]);
        Session::flash('success', get_phrase('Profile picture updated.'));
        return redirect()->back();
    }
}
