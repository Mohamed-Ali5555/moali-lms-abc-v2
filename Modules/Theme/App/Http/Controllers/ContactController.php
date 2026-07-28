<?php

namespace Modules\Theme\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        if (get_theme_settings('contact_status') === '0') {
            abort(404);
        }

        return view('theme::contact.index');
    }

    public function store(Request $request)
    {
        if (get_theme_settings('contact_status') === '0') {
            abort(404);
        }

        $input = $request->all();

        if (get_frontend_settings('recaptcha_status') == true && check_recaptcha($input['g-recaptcha-response'] ?? '') == false) {
            Session::flash('error', get_phrase('Recaptcha verification failed'));
            return redirect()->route('theme.contact.us');
        }

        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:50',
            'address' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Contact::insert([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'message' => $request->message,
        ]);

        Session::flash('success', get_phrase('Your message has been sent successfully.'));
        return redirect()->back();
    }
}
