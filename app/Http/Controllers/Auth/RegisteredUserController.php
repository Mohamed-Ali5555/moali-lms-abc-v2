<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $categories = get_student_grade_categories();
        return view('auth.register',compact('categories'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        
        $input = $request->all();
        if (get_frontend_settings('recaptcha_status') == true && check_recaptcha($input['g-recaptcha-response']) == false) {

            Session::flash('error', get_phrase('Recaptcha verification failed'));

            return redirect(route('register.form'));
        }

        $validator = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'unique:users,email'],
            'phone'        => array_merge(saudi_phone_validation_rules()),
            'national_id'  => iqama_validation_rules(),
            'category'     => student_grade_category_rule(),
            'goverment'    => ['required'],
            'address'      => ['required', 'string', 'max:255'],

            'password'     => ['required', Rules\Password::defaults()],
        ], array_merge(iqama_validation_messages(), saudi_phone_validation_messages()));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user_data = [
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'national_id'  => $request->national_id,
            'category'     => $request->category,
            'goverment'    => $request->goverment,
            'address'      => $request->address,

            'email_verified_at'=>Carbon::now(),
            'role' => 'student',
            'status' => 1,
            'password' => Hash::make($request->password),
        ];

        if(get_settings('student_email_verification') != 1){
            $user_data['email_verified_at'] = Carbon::now();
        }

        $user = User::create($user_data);


        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
