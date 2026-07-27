<?php
namespace App\Http\Controllers\Api\v1;
use App\Models\Coupon;
use Modules\BookStore\App\Models\Book;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\FileUploader;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Theme\App\Models\theme_feature;

class UserController extends Controller
{

   // update user data
    public function update_userdata(Request $request)
    {
        $response = array();
        $token = $request->bearerToken();

        if (isset($token) && $token != '') {
            $user_id = auth('sanctum')->user()->id;

            if ($request->name != "") {
                $data['name'] = htmlspecialchars($request->name, ENT_QUOTES, 'UTF-8');
            } else {
                $response['status'] = 'failed';
                $response['error_reason'] = 'Name cannot be empty';
                return $response;
            }

            $data['biography'] = $request->biography;
            $data['about'] = $request->about;
            $data['address'] = $request->address;
            $data['facebook'] = htmlspecialchars($request->facebook, ENT_QUOTES, 'UTF-8');
            $data['twitter'] = htmlspecialchars($request->twitter, ENT_QUOTES, 'UTF-8');
            $data['linkedin'] = htmlspecialchars($request->linkedin, ENT_QUOTES, 'UTF-8');

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $file_name = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $path = 'assets/upload/users/' . auth('sanctum')->user()->role . '/' . $file_name;

                // Assuming FileUploader::upload() is a method that uploads the file
                FileUploader::upload($file, $path, null, null, 300);

                // Save the path to the database
                $data['photo'] = $path;
            }

            User::where('id', $user_id)->update($data);

            $user = auth('sanctum')->user();
            $user->photo = get_photo('user_image', $user->photo);

            $updated_user = User::find($user_id);
            $updated_user['photo'] = url('public/' . $updated_user['photo']);

            $response['status'] = 'success';
            $response['user'] = $updated_user;
            $response['error_reason'] = 'None';

        } else {
            $response['status'] = 'failed';
            $response['error_reason'] = 'Unauthorized login';
        }

        return $response;
    }


        // password reset
    public function update_password(Request $request)
    {

        $token = $request->bearerToken();
        $response = array();

        if (isset($token) && $token != '') {
            $auth = auth('sanctum')->user();

            // The passwords matches
            if (!Hash::check($request->get('current_password'), $auth->password)) {
                $response['status'] = 'failed';
                $response['message'] = 'Current Password is Invalid';

                return $response;
            }

            // Current password and new password same
            if (strcmp($request->get('current_password'), $request->new_password) == 0) {
                $response['status'] = 'failed';
                $response['message'] = 'New Password cannot be same as your current password.';

                return $response;
            }

            // Current password and new password same
            if (strcmp($request->get('confirm_password'), $request->new_password) != 0) {
                $response['status'] = 'failed';
                $response['message'] = 'New Password is not same as your confirm password.';

                return $response;
            }

            $user = User::find($auth->id);
            $user->password = Hash::make($request->new_password);
            $user->save();

            $response['status'] = 'success';
            $response['message'] = 'Password Changed Successfully';

            return $response;

        } else {
            $response['status'] = 'failed';
            $response['message'] = 'Please login first';

            return $response;
        }
    }

}
