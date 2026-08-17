<?php

namespace App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\FileUploader;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Category;
use Hash;

class MyProfileController extends Controller
{
    public function index()
    {
        $page_data['user_details'] = User::find(auth()->user()->id);
        $page_data['categories'] = Category::where('parent_id',0)->get();
        $view_path                 = 'frontend.' . get_frontend_settings('theme') . '.student.my_profile.index';
        return view($view_path, $page_data);
    }

    public function update(Request $request, $user_id)
    {
        $rules = [
            'name'            => 'required',
            'email'           => 'required|email|unique:users,email,' . $user_id,
            'national_id'     => iqama_validation_rules((int) $user_id),
            'category'        => 'required',
            'goverment'       => 'required',
            'phone'           => array_merge(saudi_phone_validation_rules()),
            'old_password'    => 'required_with:new_password',
            'new_password'    => 'nullable',
        ];
        $validator = Validator::make($request->all(), $rules, array_merge(
            iqama_validation_messages(),
            saudi_phone_validation_messages()
        ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $user = User::find($user_id);
      
        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Old password does not match')->withInput();
            }
            $data['password']     = Hash::make($request->new_password);
        }

        $data['name']             = $request->name;
        $data['email']            = $request->email;
        $data['phone']            = $request->phone;
        $data['national_id']      = $request->national_id;
        $data['category']         = $request->category;
        $data['goverment']        = $request->goverment;
        $data['address']          = $request->address;
        $data['phone']            = $request->phone;


        // $data['website']          = $request->website;
        // $data['facebook']         = $request->facebook;
        // $data['twitter']          = $request->twitter;
        // $data['linkedin']         = $request->linkedin;
        // $data['skills']           = $request->skills;
        // $data['about']            = $request->about;

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
