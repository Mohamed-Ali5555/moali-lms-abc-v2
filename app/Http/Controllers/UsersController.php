<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\FileUploader;
use App\Models\Payout;
use App\Models\Permission;
use App\Models\Setting;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Watch_history;
use App\Models\QuizSubmission;
use App\Models\Payment_history;
use App\Providers\RouteServiceProvider;
use App\Services\WaPilot\WhatsAppNotifier;

class UsersController extends Controller
{

    public function admin_index(Request $request)
    {
        $query = User::where('role', 'admin');

        $super_admin_email = get_super_admin_email();
        if ($super_admin_email !== '') {
            $query->whereRaw('LOWER(TRIM(email)) != ?', [$super_admin_email]);
        }

        if(request()->filled('search')){
            $search = $request->search;
            $query->where(function($q) use($search){
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('email', 'LIKE', '%' . $search. '%')
                    ->orWhere('national_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('goverment', 'LIKE', '%' . $search . '%');
            });
         }

        $page_data['admins'] = $query->paginate(10)->withQueryString();
        return view('admin.admin.index', $page_data);
    }

    public function student_not_enroll(Request $request){
        $activeCourses = Course::where('status', 'active')->pluck('id');
              $query = User::where('role', 'student')->whereDoesntHave('enrollments',function($q) use ($activeCourses){
                    $q->whereIn('course_id', $activeCourses);
                });

              if(request()->filled('search')){
                 $search = $request->search;
                 $query->where(function($q) use($search){
                     $q->where('name', 'LIKE', '%' . $search . '%')
                         ->orWhere('phone', 'LIKE', '%' . $search . '%')
                       
                         ->orWhere('national_id', 'LIKE', '%' . $search . '%');

                 });
              }


         $users = $query ->get();
        return view('admin.student.student_not_enroll', compact('users'));
    }
    public function admin_create()
    {
        return view('admin.admin.create_admin');
    }
    public function admin_store(Request $request)
    {

        $validated = $request->validate([
            'name'        => 'required|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8',
            'goverment'   => 'required|in:' . implode(',', get_saudi_regions()),
            'national_id' => iqama_validation_rules(),
            'gender'      => 'nullable|in:1,2',
            'phone'       => 'nullable|regex:/^05\d{8}$/',
        ], iqama_validation_messages());

        $data['name']        = $request->name;
        $data['phone']       = $request->phone;
        $data['address']     = $request->address;
        $data['goverment']   = $request->goverment;
        $data['national_id'] = $request->national_id;
        $data['gender']      = $request->gender;
        $data['email']       = $request->email;
        $data['password']    = Hash::make($request->password);

        $data['role']     = 'admin';
        $data['status']     = '1';

        if (isset($request->photo) && $request->hasFile('photo')) {
            $path = "uploads/users/instructor/" . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $path, 400, null, 200, 200);
            $data['photo'] = $path;
        }

        $done = User::insert($data);

        if ($done) {
            $admin_id = User::latest('id')->first();
            Permission::insert(['admin_id' => $admin_id->id]);
            Session::flash('success', get_phrase('تم إضافة الموظف بنجاح — يمكنك الآن تعيين الصلاحيات'));
            return redirect()->route('admin.admins.permission', ['user_id' => $admin_id->id]);
        }
        Session::flash('error', get_phrase('حدث خطأ أثناء إضافة الموظف'));
        return redirect()->back()->withInput();
    }

    public function admin_edit($id)
    {
        if (is_super_admin($id)) {
            Session::flash('error', get_phrase('لا يمكن تعديل هذا الحساب'));
            return redirect()->route('admin.admins.index');
        }

        $page_data['admin'] = User::where('id', $id)->first();
        return view('admin.admin.edit_admin', $page_data);
    }
    public function admin_update(Request $request, $id)
    {
        if (is_super_admin($id)) {
            Session::flash('error', get_phrase('لا يمكن تعديل هذا الحساب'));
            return redirect()->route('admin.admins.index');
        }

        $validated = $request->validate([
            'name'        => 'required|max:255',
            'email'       => "required|email|unique:users,email,$id",
            'goverment'   => 'required|in:' . implode(',', get_saudi_regions()),
            'national_id' => iqama_validation_rules((int) $id),
            'gender'      => 'nullable|in:1,2',
            'phone'       => 'nullable|regex:/^05\d{8}$/',
        ], iqama_validation_messages());

        $data['name']        = $request->name;
        $data['about']       = $request->about;
        $data['phone']       = $request->phone;
        $data['address']     = $request->address;
        $data['goverment']   = $request->goverment;
        $data['national_id'] = $request->national_id;
        $data['gender']      = $request->gender;
        $data['email']       = $request->email;
        $data['facebook'] = $request->facebook;
        $data['twitter']  = $request->twitter;
        $data['website']  = $request->website;
        $data['linkedin'] = $request->linkedin;
        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Old password does not match')->withInput();
            }
            $data['password']     = Hash::make($request->new_password);
        }
        if (isset($request->photo) && $request->hasFile('photo')) {
            remove_file(User::where('id', $id)->first()->photo);
            $path = "uploads/users/instructor/" . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $path, 400, null, 200, 200);
            $data['photo'] = $path;
        }

        User::where('id', $request->id)->update($data);
        Session::flash('success', get_phrase('Admin update successfully'));
        return redirect()->route('admin.admins.index');
    }

    public function admin_delete($id)
    {
        if (is_super_admin($id)) {
            Session::flash('error', get_phrase('لا يمكن حذف هذا الحساب'));
            return redirect()->back();
        }

        $threads = MessageThread::where('contact_one', $id)
                    ->orWhere('contact_two', $id)
                    ->pluck('id');

        if ($threads->isNotEmpty()) {
            Message::whereIn('thread_id', $threads)->delete();
            MessageThread::whereIn('id', $threads)->delete();
        }

        $done = User::where('id', $id)->delete();
        if ($done) {
            Permission::where('admin_id', $id)->delete();
        }
        Session::flash('success', get_phrase('Admin delete successfully'));
        return redirect()->back();
    }

    public function admin_permission($user_id)
    {
        if (is_super_admin($user_id)) {
            Session::flash('error', get_phrase('لا يمكن تعديل صلاحيات هذا الحساب'));
            return redirect()->route('admin.admins.index');
        }

        $page_data['admin'] = User::where('id', $user_id)->firstOrNew();
        return view('admin.admin.permission', $page_data);
    }
    public function admin_permission_store(Request $request)
    {
        $user_id = $request->user_id;

        if (is_super_admin($user_id)) {
            return false;
        }

        $permission = Permission::where('admin_id', $user_id)->first();

        if ($permission) {
            $set_permission = json_decode($permission->permissions, true) ?? [];
            if (in_array($request->permission, $set_permission)) {
                $pos = array_search($request->permission, $set_permission);
                array_splice($set_permission, $pos, 1);
            } else {
                array_push($set_permission, $request->permission);
            }
            Permission::where('admin_id', $user_id)->update(['permissions' => json_encode(array_values($set_permission))]);
            clear_lms_cache('permissions');
            return true;
        } else {
            $set_per = json_encode([$request->permission]);
            Permission::insert(['admin_id' => $user_id, 'permissions' => $set_per]);
            clear_lms_cache('permissions');
            return true;
        }
    }

    public function admin_permission_preset(Request $request, $user_id)
    {
        if (is_super_admin($user_id)) {
            return response()->json(['success' => false, 'message' => 'لا يمكن تعديل صلاحيات هذا الحساب'], 403);
        }

        $presetKey = $request->input('preset');
        $presets = get_admin_permission_presets();

        if (!isset($presets[$presetKey])) {
            return response()->json(['success' => false, 'message' => 'قالب الصلاحيات غير موجود'], 422);
        }

        $permissions = $presetKey === 'full_access'
            ? get_all_admin_permission_keys()
            : array_values(array_unique($presets[$presetKey]['permissions']));
        $permission = Permission::where('admin_id', $user_id)->first();

        if ($permission) {
            Permission::where('admin_id', $user_id)->update(['permissions' => json_encode($permissions)]);
        } else {
            Permission::insert(['admin_id' => $user_id, 'permissions' => json_encode($permissions)]);
        }

        clear_lms_cache('permissions');

        return response()->json([
            'success' => true,
            'permissions' => $permissions,
            'message' => 'تم تطبيق قالب: ' . $presets[$presetKey]['label'],
        ]);
    }

    public function instructor_index(Request $request)
    {
        $query = User::where('role', 'instructor');
        if(request()->filled('search')){
            $search = $request->search;
            $query->where(function($q) use($search){
                $q->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%')
                ->orWhere('national_id', 'LIKE', '%' . $search . '%')
                ->orWhere('goverment', 'LIKE', '%' . $search . '%');
            });
        }

        $page_data['instructors'] = $query->paginate(10)->withQueryString();
        return view('admin.instructor.index', $page_data);
    }

    public function instructor_create()
    {
        return view('admin.instructor.create_instructor');
    }
    public function instructor_edit($id = '')
    {
        $page_data['instructor'] = User::where('id', $id)->first();
        return view('admin.instructor.edit_instructor', $page_data);
    }
    public function instructor_store(Request $request, $id = '')
    {
        $validated = $request->validate([
            'name'        => 'required|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8',
            'goverment'   => 'required|in:' . implode(',', get_saudi_regions()),
            'national_id' => iqama_validation_rules(),
            'gender'      => 'nullable|in:1,2',
            'phone'       => 'nullable|regex:/^05\d{8}$/',
        ], iqama_validation_messages());

        if(get_settings('student_email_verification') != 1){
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        }

        $data['name']        = $request->name;
        $data['about']       = $request->about;
        $data['phone']       = $request->phone;
        $data['address']     = $request->address;
        $data['goverment']   = $request->goverment;
        $data['national_id'] = $request->national_id;
        $data['gender']      = $request->gender;
        $data['email']       = $request->email;
        $data['facebook']    = $request->facebook;
        $data['twitter']     = $request->twitter;
        $data['website']     = $request->website;
        $data['linkedin']    = $request->linkedin;
        $data['paymentkeys'] = json_encode($request->paymentkeys);
        $data['status']     = '1';

        $data['password'] = Hash::make($request->password);
        $data['role']     = 'instructor';

        if (isset($request->photo) && $request->hasFile('photo')) {
            $path = "uploads/users/instructor/" . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $path, 400, null, 200, 200);
            $data['photo'] = $path;
        }
        $user = User::create($data);

        if(get_settings('student_email_verification') == 1) {
            $user->sendEmailVerificationNotification();
        }

        Session::flash('success', get_phrase('Instructor add successfully'));

        return redirect()->route('admin.instructor.index');
    }

    public function instructor_update(Request $request, $id = '')
    {

        $validated = $request->validate([
            'name'        => 'required|max:255',
            'email'       => "required|email|unique:users,email,$id",
            'goverment'   => 'required|in:' . implode(',', get_saudi_regions()),
            'national_id' => iqama_validation_rules((int) $id),
            'gender'      => 'nullable|in:1,2',
            'phone'       => 'nullable|regex:/^05\d{8}$/',
        ], iqama_validation_messages());

        $data['name']        = $request->name;
        $data['about']       = $request->about;
        $data['phone']       = $request->phone;
        $data['address']     = $request->address;
        $data['goverment']   = $request->goverment;
        $data['national_id'] = $request->national_id;
        $data['gender']      = $request->gender;
        $data['email']       = $request->email;
        $data['facebook']    = $request->facebook;
        $data['twitter']     = $request->twitter;
        $data['website']     = $request->website;
        $data['linkedin']    = $request->linkedin;
        $data['paymentkeys'] = json_encode($request->paymentkeys);

        if ($request->filled('new_password')) {
            $user = User::where('id', $id)->first();
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->with('error', 'Old password does not match')->withInput();
            }
            $data['password'] = Hash::make($request->new_password);
        }

        if (isset($request->photo) && $request->hasFile('photo')) {
            remove_file(User::where('id', $id)->first()->photo);
            $path = "uploads/users/instructor/" . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $path, 400, null, 200, 200);
            $data['photo'] = $path;
        }

        User::where('id', $id)->update($data);
        Session::flash('success', get_phrase('Instructor update successfully'));
        return redirect()->route('admin.instructor.index');
    }

    public function instructor_delete($id)
    {
        $threads = MessageThread::where('contact_one', $id)
                    ->orWhere('contact_two', $id)
                    ->pluck('id');

        if ($threads->isNotEmpty()) {
            Message::whereIn('thread_id', $threads)->delete();
            MessageThread::whereIn('id', $threads)->delete();
        }

        User::where('id', $id)->delete();
        Session::flash('success', get_phrase('Instructor delete successfully'));
        return redirect()->back();
    }

    public function instructor_view_course(Request $request)
    {
        $course = Course::where('user_id', $request->id)->get();
    }

    public function instructor_payout(Request $request)
    {
        $start_date                              = strtotime('first day of this month');
        $end_date                                = strtotime('last day of this month');
        $page_data['start_date']                 = $start_date;
        $page_data['end_date']                   = $end_date;
        $page_data['instructor_payout_complete'] = Payout::where('status', 1)->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
            ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))->paginate(10);
        $page_data['instructor_payout_incomplete'] = Payout::where('status', 0)->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
            ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))->paginate(10);
        return view('admin.instructor.payout', $page_data);
    }
    public function instructor_payout_filter(Request $request)
    {

        $date                    = explode('-', $request->eDateRange);
        $start_date              = strtotime($date[0] . ' 00:00:00');
        $end_date                = strtotime($date[1] . ' 23:59:59');
        $page_data['start_date'] = $start_date;
        $page_data['end_date']   = $end_date;

        $page_data['instructor_payout_complete'] = Payout::where('status', 1)->where('created_at', '>=', date('Y-m-d H:i:s', $start_date))
            ->where('created_at', '<=', date('Y-m-d H:i:s', $end_date))->paginate(10);
        $page_data['instructor_payout_incomplete'] = Payout::where('status', 0)->paginate(10);

        return view('admin.instructor.payout', $page_data);
    }

    public function instructor_payout_invoice($id = '')
    {
        if ($id != '') {
            $page_data['invoice_info'] = Payout::where('status', 1)->first();
            $page_data['invoice_data'] = Payout::where('status', 1)->get();
            $page_data['invoice_id']   = $id;

            return view('admin.instructor.instructor_invoice', $page_data);
        }
    }

    public function instructor_payment(Request $request)
    {
        $id              = $request->user_id;
        $payable_amount  = $request->amount;
        $start_timestamp = time();
        $end_timestamp   = time();

        $payment_details = [
            'items'          => [
                [
                    'id'                  => $id,
                    'title'               => get_phrase('Pay for instructor payout'),
                    'subtitle'            => get_phrase(''),
                    'price'               => $payable_amount,
                    'qty'                 => 1,
                    'discount_price'      => $payable_amount,
                    'discount_percentage' => 0,
                ],
            ],
            'custom_field'   => [
                'start_date' => date('Y-m-d H:i:s', $start_timestamp),
                'end_date'   => date('Y-m-d H:i:s', $end_timestamp),
                'user_id'    => auth()->user()->id,
                'payout_id'  => $request->payout_id,

            ],
            'success_method' => [
                'model_name'    => 'InstructorPayment',
                'function_name' => 'instructor_payment',
            ],
            'tax'            => 0,
            'coupon'         => null,
            'payable_amount' => $payable_amount,
            'cancel_url'     => route('admin.instructor.payout'),
            'success_url'    => route('payment.success'),
        ];
        session(['payment_details' => $payment_details]);
        return redirect()->route('payment');
    }

    public function instructor_setting()
    {
        $page_data['allow_instructor']   = Setting::where('type', 'allow_instructor')->first();
        $page_data['application_note']   = Setting::where('type', 'instructor_application_note')->first();
        $page_data['instructor_revenue'] = Setting::where('type', 'instructor_revenue')->first();
        return view('admin.instructor.instructor_setting', $page_data);
    }

    public function instructor_setting_store(Request $request)
    {

        if ($request->first == 'item_1') {

            $key_found = Setting::where('type', 'instructor_application_note')->exists();
            if ($key_found) {
                $data['description'] = $request->instructor_application_note;

                Setting::where('type', 'instructor_application_note')->update($data);
            } else {
                $data['type']        = 'instructor_application_note';
                $data['description'] = $request->instructor_application_note;

                Setting::insert($data);
            }

            $key_founds = Setting::where('type', 'allow_instructor')->exists();
            if ($key_founds) {
                $data['description'] = $request->allow_instructor;

                Setting::where('type', 'allow_instructor')->update($data);
            } else {

                $data['type']        = 'allow_instructor';
                $data['description'] = $request->allow_instructor;

                Setting::insert($data);
            }
        }
        if ($request->second == 'item_2') {

            $key_found = Setting::where('type', 'instructor_revenue')->exists();
            if ($key_found) {
                $data['description'] = $request->instructor_revenue;

                Setting::where('type', 'instructor_revenue')->update($data);
            } else {
                $data['type']        = 'instructor_revenue';
                $data['description'] = $request->instructor_revenue;

                Setting::insert($data);
            }
        }

        Session::flash('success', get_phrase('Instructor setting updated'));
        return redirect()->back();
    }

    public function instructor_application()
    {
        return view('admin.instructor.application');
    }
    public function instructor_application_approve($id)
    {
        $query         = Application::where('id', $id);
        $update_status = $query->update(['status' => 1]);
        if ($update_status) {
            $user_id = $query->first();
            User::where('id', $user_id->user_id)->update(['role' => 'instructor']);
            Session::flash('success', get_phrase('Application approve successfully'));
        }
        return redirect()->back();
    }
    public function instructor_application_delete($id)
    {
        Application::where('id', $id)->delete();
        Session::flash('success', get_phrase('Application delete successfully'));
        return redirect()->back();
    }
    public function instructor_application_download($id)
    {
        $path = Application::where('id', $id)->first();

        if (file_exists(public_path($path->document))) {
            return response()->download(public_path($path->document));
        } else {
            Session::flash('error', get_phrase('File does not exists'));
            return redirect()->back();
        }
    }

    public function student_index(Request $request)
    {
        $query = User::where('role', 'student');
        if(request()->filled('search')){
            $search = $request->search;
            $query->where(function($q) use($search){
                $q->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('phone', 'LIKE', '%' . $search . '%')
                ->orWhere('national_id', 'LIKE', '%' . $search. '%');
            });
        }

        $page_data['students'] = $query->paginate(10)->withQueryString();
        return view('admin.student.index', $page_data);
    }

    public function student_create()
    {
        $categories = get_student_grade_categories();

        return view('admin.student.create_student',compact('categories'));
    }
    public function student_edit($id = '')
    {
        $categories = get_student_grade_categories();
        $page_data['student'] = User::where('id', $id)->first();
        return view('admin.student.edit_student', $page_data,compact('categories'));
    }
    public function student_store(Request $request, $id = '')
    {

        $validated = $request->validate([
            'name'            => 'required|max:255',
            'national_id'     => iqama_validation_rules(),
            'category'        => student_grade_category_rule(),
            'goverment'       => 'required',
            'gender'          => 'required|in:1,2',
            'phone'           => array_merge(saudi_phone_validation_rules()),
            'email'           => 'required|email|unique:users',
            'password'        => 'required',
        ], array_merge(iqama_validation_messages(), saudi_phone_validation_messages()));

        if(get_settings('student_email_verification') != 1){
            $data['email_verified_at'] = date('Y-m-d H:i:s');
        }

        $data['name']            = $request->name;
        $data['phone']           = $request->phone;
        $data['category']        = $request->category;
        $data['goverment']       = $request->goverment;
        $data['national_id']     = $request->national_id;
        $data['address']         = $request->address;
        $data['email']           = $request->email;
        $data['gender']          = $request->gender;

        $data['status']          = '1';

        $data['password'] = Hash::make($request->password);
        $data['role']     = 'student';

       if ($request->photo) {

            $data['photo'] = "uploads/users/student/" . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $data['photo'], 400, null, 200, 200);
        }
        $user = User::create($data);

        if(get_settings('student_email_verification') == 1) {
            $user->sendEmailVerificationNotification();
        }

        Session::flash('success', get_phrase('Student add successfully'));

        return redirect()->route('admin.student.index');
    }

    public function student_update(Request $request, $id = '')
    {
        $validated = $request->validate([
            'name'             => 'required|max:255',
            'national_id'      => iqama_validation_rules((int) $id),
            'category'         => student_grade_category_rule(),
            'goverment'        => 'required',
            'phone'            => array_merge(saudi_phone_validation_rules()),
            'email'            => "required|email|unique:users,email,$id",
            'gender'          => 'required|in:1,2',
            // 'old_password'    => 'required_with:new_password',
            'new_password'    => 'nullable',
        ], array_merge(iqama_validation_messages(), saudi_phone_validation_messages()));
       $user = User::find($id);

        $data['name']            = $request->name;
        $data['phone']           = $request->phone;
        $data['category']        = $request->category;
        $data['goverment']       = $request->goverment;
        $data['national_id']     = $request->national_id;
        $data['address']         = $request->address;
        $data['email']           = $request->email;
        $data['gender']          = $request->gender;

        if ($request->filled('new_password')) {
            // if (!Hash::check($request->old_password, $user->password)) {
            //     return redirect()->back()->with('error', 'Old password does not match')->withInput();
            // }
            $data['password']     = Hash::make($request->new_password);
        }
        if (isset($request->photo) && $request->hasFile('photo')) {
                $user = User::find($id);
                if ($user && $user->photo && file_exists(public_path($user->photo))) {
                    unlink(public_path($user->photo));
                }
            $path = "uploads/users/student/" . nice_file_name($request->name, $request->photo->extension());
            FileUploader::upload($request->photo, $path, 400, null, 200, 200);
            $data['photo'] = $path;
        }

        $user->update($data);
        Session::flash('success', get_phrase('Student update successfully'));
        return redirect()->route('admin.student.index');
    }

    public function student_delete($id)
    {
        $threads = MessageThread::where('contact_one', $id)
                    ->orWhere('contact_two', $id)
                    ->pluck('id');

        if ($threads->isNotEmpty()) {
            Message::whereIn('thread_id', $threads)->delete();
            MessageThread::whereIn('id', $threads)->delete();
        }

        $query = User::find($id);
        if ($query && $query->photo && file_exists(public_path($query->photo))) {
            unlink(public_path($query->photo));
        }

        Enrollment::where('user_id',$id)->delete();

        $query->delete();
        return redirect(route('admin.student.index'))->with('success', 'تم حذف الطالب بنجاح');
    }

    public function student_delete_remove_device($id)
    {
        $query = User::where('id', $id)->first();
        $query->current_device_id = null;
        $query->save();
        return redirect(route('admin.student.index'))->with('success', 'تم مسح الجهاز المسجل به بنجاح');
    }

    public function studentLogin($id)
    {
        $user = User::find($id);

            if (!$user) {
                return redirect()->back()->with('error', 'Student not found');
            }
            Auth::login($user);
            return redirect(RouteServiceProvider::HOME);
    }

    public function view_course($id)
    {
        $student = User::where('role', 'student')->findOrFail($id);

        $courses = Enrollment::query()
            ->where('user_id', $id)
            ->with(['course.lessons', 'course.category'])
            ->latest()
            ->get()
            ->filter(fn ($enrollment) => $enrollment->course !== null)
            ->values();

        $courseIds = $courses->pluck('course_id')->unique()->filter()->values();

        $watchHistories = Watch_history::query()
            ->where('student_id', $id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        $completedByCourse = [];
        foreach ($watchHistories as $courseId => $history) {
            $ids = json_decode($history->completed_lesson ?? '[]', true);
            $completedByCourse[$courseId] = array_map('strval', is_array($ids) ? $ids : []);
        }

        $progressSum = 0;
        $completedLessons = 0;
        $totalLessons = 0;

        foreach ($courses as $enrollment) {
            $progressSum += (float) progress_bar_admin($enrollment->course_id, $id);
            $lessonIds = $enrollment->course->lessons->pluck('id')->map(fn ($lessonId) => (string) $lessonId);
            $totalLessons += $lessonIds->count();
            $doneIds = $completedByCourse[$enrollment->course_id] ?? [];
            $completedLessons += $lessonIds->filter(fn ($lessonId) => in_array($lessonId, $doneIds, true))->count();
        }

        $coursesCount = $courses->count();

        return view('admin.student.view_course', [
            'student' => $student,
            'courses' => $courses,
            'completedByCourse' => $completedByCourse,
            'avgProgress' => $coursesCount ? round($progressSum / $coursesCount, 1) : 0,
            'completedLessons' => $completedLessons,
            'totalLessons' => $totalLessons,
        ]);
    }
    public function student_enrol()
    {
        return view('admin.enroll.course_enrollment');
    }
    public function student_get(Request $request)
    {

        $user = User::where('role', 'student')->where('name', 'LIKE', '%' . $request->searchVal . '%')->get();

        foreach ($user as $row) {
            $response[] = ['id' => $row->id, 'text' => $row->name];
        }
        return json_encode($response);
    }

    // public function student_post(Request $request)
    // {
    //     $message = [];
    //     for ($i = 0; $i < count($request->user_id); $i++) {
    //         for ($j = 0; $j < count($request->course_id); $j++) {
    //             $data['user_id']    = $request->user_id[$i];
    //             $data['course_id']  = $request->course_id[$j];
    //             $data['entry_date'] = time();

    //             $course_details = $course_details = get_course_info($request->course_id[$j]);

    //             if ($course_details->expiry_period > 0) {
    //                 $days = $course_details->expiry_period * 30;
    //                 $data['expiry_date'] = strtotime("+" . $days . " days");
    //             } else {
    //                 $data['expiry_date'] = null;
    //             }

    //             $user  = Enrollment::where('user_id', $request->user_id[$i])->where('course_id', $request->course_id[$j])->exists();
    //             if (!$user) {

    //                 Enrollment::insert($data);
    //             }else{
    //                 return back()->with('error', $request->user_id[$i] . 'student is alreedy added to this course');
    //             }
    //         }
    //     }

    //     Session::flash('success', get_phrase('Student add successfully'));
    //     return redirect()->route('admin.enroll.history');
    // }

    public function student_post(Request $request)
    {
        $messages = [];

        for ($i = 0; $i < count($request->user_id); $i++) {
            for ($j = 0; $j < count($request->course_id); $j++) {
                $data['user_id']    = $request->user_id[$i];
                $data['course_id']  = $request->course_id[$j];
                $data['entry_date'] = time();

                $course_details = get_course_info($request->course_id[$j]);

                if ($course_details->expiry_period > 0) {
                    $days = $course_details->expiry_period * 30;
                    $data['expiry_date'] = strtotime("+" . $days . " days");
                } else {
                    $data['expiry_date'] = null;
                }

                $exists = Enrollment::where('user_id', $request->user_id[$i])
                    ->where('course_id', $request->course_id[$j])
                    ->exists();

                if (!$exists) {
                    Enrollment::insert($data);
                    $student = User::find($request->user_id[$i]);
                    $course = Course::find($request->course_id[$j]);
                    if ($student && $course) {
                        app(WhatsAppNotifier::class)->notifyEnrollment($student, $course);
                    }
                } else {
                    $userName   = User::find($request->user_id[$i])->name ?? $request->user_id[$i];
                    $courseName = $course_details->title ?? $request->course_id[$j];

                    $messages[] = "الطالب {$userName} مسجل بالفعل في كورس {$courseName}";
                }
            }
        }

        if (!empty($messages)) {
            return back()->with('error', implode(' | ', $messages));
        }

        Session::flash('success', get_phrase('Student add successfully'));
        return redirect()->route('admin.enroll.history');
    }


    public function enroll_history(Request $request)
    {
        $query = Enrollment::with(['user', 'course']);
        if($request->has('id')){
            $query->where('course_id',$request->id);
        }

        // Search by student name or phone
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $date_filter = $request->get('date_filter', 'any_time');
        $default_start_date = strtotime('first day of this month');
        $default_end_date   = strtotime('last day of this month');

        $page_data['start_date'] = $default_start_date;
        $page_data['end_date']   = $default_end_date;

        // Apply a date range only when the user explicitly selects one.
        if (
            $date_filter !== 'any_time'
            && $request->filled('eDateRange')
            && str_contains($request->eDateRange, '-')
        ) {
            $date                    = explode('-', $request->eDateRange);
            $start_date              = strtotime(trim($date[0]) . ' 00:00:00');
            $end_date                = strtotime(trim($date[1]) . ' 23:59:59');
            $page_data['start_date'] = $start_date;
            $page_data['end_date']   = $end_date;
            $page_data['enroll_history'] = $query->where('entry_date', '>=', $start_date)
                ->where('entry_date', '<=', $end_date)
                ->paginate(10)->appends($request->query());
        } else {
            $date_filter = 'any_time';
            $page_data['enroll_history'] = $query->paginate(10)->appends($request->query());
        }
        $page_data['date_filter'] = $date_filter;
        $page_data['search'] = $request->get('search', '');
        $page_data['course_id'] = $request->get('id');
        $page_data['courses_for_filter'] = Course::whereHas('enrollments')->orderBy('title')->get();
        if ($page_data['course_id']) {
            $page_data['course_for_extend'] = Course::find($page_data['course_id']);
        } else {
            $page_data['course_for_extend'] = null;
        }
        return view('admin.enroll.enroll_history', $page_data);
    }



    public function enroll_history_delete($id)
    {
        $enroll = Enrollment::where('id', $id)->first();
        // dd($enroll->user_id);
        if($enroll){
           $paymentHistoryId = $enroll->payment_history_id;

            Watch_history::where(['student_id'=>$enroll->user_id,'course_id'=>$enroll->course_id])->delete();

            QuizSubmission::where('user_id',$enroll->user_id)->whereHas('lesson',function($q) use($enroll){
                $q->where('course_id',$enroll->course_id);
            })->delete();


              $enroll->delete();

           if ($paymentHistoryId) {
                $paymentHistory = Payment_history::with('items')->find($paymentHistoryId);

                if ($paymentHistory) {
                    $paymentHistory->items()
                        ->where('productable_type', 'App\Models\Course')
                        ->where('productable_id', $enroll->course_id)
                        ->delete();

                    if ($paymentHistory->items()->count() == 0) {
                        $paymentHistory->delete();
                    }
                }
        }
        }
        Session::flash('success', get_phrase('Enroll delete successfully'));
        return redirect()->back();
    }

    public function enroll_history_unenroll_all($course_id)
    {
        if (!has_permission('admin.enroll.history.delete')) {
            Session::flash('error', get_phrase('You do not have permission to perform this action.'));
            return redirect()->back();
        }

        $course = Course::find($course_id);
        if (!$course) {
            Session::flash('error', get_phrase('Course not found.'));
            return redirect()->route('admin.enroll.history');
        }

        $enrollments = Enrollment::where('course_id', $course_id)->get();
        $count = 0;

        foreach ($enrollments as $enroll) {
            $paymentHistoryId = $enroll->payment_history_id;
            $enrollCourseId = $enroll->course_id;
            $enrollUserId = $enroll->user_id;

            Watch_history::where(['student_id' => $enrollUserId, 'course_id' => $enrollCourseId])->delete();

            QuizSubmission::where('user_id', $enrollUserId)->whereHas('lesson', function ($q) use ($enrollCourseId) {
                $q->where('course_id', $enrollCourseId);
            })->delete();

            $enroll->delete();

            if ($paymentHistoryId) {
                $paymentHistory = Payment_history::with('items')->find($paymentHistoryId);

                if ($paymentHistory) {
                    $paymentHistory->items()
                        ->where('productable_type', 'App\Models\Course')
                        ->where('productable_id', $enrollCourseId)
                        ->delete();

                    if ($paymentHistory->items()->count() == 0) {
                        $paymentHistory->delete();
                    }
                }
            }
            $count++;
        }

        Session::flash('success', get_phrase('Unenrolled ____ students from course.', [$count]));
        return redirect()->route('admin.enroll.history', ['id' => $course_id]);
    }

    public function enroll_history_update_expiry_date(Request $request, $id)
    {
        $enroll = Enrollment::where('id', $id)->first();
        
        if(!$enroll){
            Session::flash('error', get_phrase('Enrollment not found.'));
            return redirect()->route('admin.enroll.history');
        }

        $expiry_date = null;
        if($request->expiry_date){
            $expiry_date = strtotime($request->expiry_date);
        }

        $enroll->expiry_date = $expiry_date;
        $enroll->save();

        Session::flash('success', get_phrase('Expiry date updated successfully.'));
        return redirect()->route('admin.enroll.history');
    }

    public function enroll_history_extend_course(Request $request, $course_id)
    {
        $request->validate([
            'add_days' => 'required|integer|min:1|max:3650',
        ], [
            'add_days.required' => get_phrase('Please enter number of days to add'),
            'add_days.integer'  => get_phrase('Days must be a number'),
            'add_days.min'      => get_phrase('Days must be at least 1'),
        ]);

        $course = Course::find($course_id);
        if (!$course) {
            Session::flash('error', get_phrase('Course not found.'));
            return redirect()->route('admin.enroll.history');
        }

        $enrollments = Enrollment::where('course_id', $course_id)->get();
        $add_seconds = (int) $request->add_days * 24 * 60 * 60;
        $updated = 0;

        foreach ($enrollments as $enroll) {
            if ($enroll->expiry_date) {
                $enroll->expiry_date = $enroll->expiry_date + $add_seconds;
            } else {
                $enroll->expiry_date = time() + $add_seconds;
            }
            $enroll->save();
            $updated++;
        }

        Session::flash('success', get_phrase('Period extended for ____ subscribers.', [$updated]));
        return redirect()->route('admin.enroll.history', ['id' => $course_id]);
    }

    public function manage_profile()
    {
        return view('admin.profile.index');
    }
    public function manage_profile_update(Request $request)
    {
        if ($request->type == 'general') {
            $profile['name']      = $request->name;
            $profile['email']     = $request->email;
            $profile['facebook']  = $request->facebook;
            $profile['linkedin']  = $request->linkedin;
            $profile['twitter']  = $request->twitter;
            $profile['about']     = $request->about;
            $profile['skills']    = $request->skills;
            $profile['biography'] = $request->biography;

            if ($request->photo) {
                if (isset($request->photo) && $request->photo != '') {
                    $profile['photo'] = "uploads/users/admin/" . nice_file_name($request->title, $request->photo->extension());
                    FileUploader::upload($request->photo, $profile['photo'], 400, null, 200, 200);
                }
            }
            User::where('id', auth()->user()->id)->update($profile);
        } else {
            $old_pass_check = Auth::attempt(['email' => auth()->user()->email, 'password' => $request->current_password]);

            if (!$old_pass_check) {
                Session::flash('error', get_phrase('Current password wrong.'));
                return redirect()->back();
            }

            if ($request->new_password != $request->confirm_password) {
                Session::flash('error', get_phrase('Confirm password not same'));
                return redirect()->back();
            }

            $password = Hash::make($request->new_password);
            User::where('id', auth()->user()->id)->update(['password' => $password]);
        }
        Session::flash('success', get_phrase('Your changes has been saved.'));
        return redirect()->back();
    }
}
