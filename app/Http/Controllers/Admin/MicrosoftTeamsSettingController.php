<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MicrosoftTeamsMeetingController;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MicrosoftTeamsSettingController extends Controller
{
    public function settings()
    {
        return view('admin.setting.microsoft_teams_settings');
    }

    public function settings_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teams_tenant_id'       => 'required|string',
            'teams_client_id'       => 'required|string',
            'teams_client_secret'   => 'required|string',
            'teams_organizer_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('tab', 'settings');
        }

        foreach ($request->only(['teams_tenant_id', 'teams_client_id', 'teams_client_secret', 'teams_organizer_email']) as $name => $value) {
            if (Setting::where('type', $name)->exists()) {
                Setting::where('type', $name)->update(['description' => $value]);
            } else {
                Setting::insert(['type' => $name, 'description' => $value, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        clear_lms_cache('settings');

        Session::flash('success', get_phrase('Microsoft Teams settings has been configured'));
        return redirect(route('admin.teams.settings'))->with('tab', 'settings');
    }

    public function test_connection()
    {
        $token = MicrosoftTeamsMeetingController::createToken();

        if ($token) {
            Session::flash('success', get_phrase('Connection successful! Access token generated from Microsoft Graph.'));
        } else {
            Session::flash('error', get_phrase('Connection failed. Please check the Tenant ID, Client ID and Client Secret.'));
        }

        return redirect(route('admin.teams.settings'))->with('tab', 'test');
    }
}
