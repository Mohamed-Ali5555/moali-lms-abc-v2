<?php

namespace Modules\Theme\App\Http\Controllers;
use App\Models\FileUploader;
use App\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Theme\App\Models\theme_setting;
use Modules\Theme\App\Models\theme_social;
use Modules\Theme\App\Models\theme_feature;
use Modules\Theme\App\Models\theme_legal_section;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SettingController extends Controller
{
  // start settings section
   public function settings()
    {
        $allowedCurrencies = ['EGP', 'SAR', 'AED', 'USD', 'EUR'];
        $currencies = DB::table('currencies')
            ->whereIn('code', $allowedCurrencies)
            ->orderByRaw("FIELD(code, 'EGP', 'SAR', 'AED', 'USD', 'EUR')")
            ->get(['code', 'name', 'symbol']);

        return view('theme::setting.theme_setting', compact('currencies'));
    }
    public function settings_store(Request $request, $param1 = '', $id = '')
    {
        $data = $request->except('_token');

        if ($param1 == 'theme_settings') {
            // Validate file uploads if present
            $request->validate([
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:48048',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:48048',
                'dark_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:48048',
                'dark_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:48048',
                'name' => 'required|string|max:255'
            ]);
            foreach ($data as $key => $item) {
                // Handle file uploads for theme settings
                if (in_array($key, ['thumbnail', 'logo', 'dark_thumbnail', 'dark_logo']) && $request->hasFile($key)) {
                    $file = $request->file($key);
                    $filePath = "uploads/theme-thumbnail/" . nice_file_name($request->input('name'), $file->extension());

                    // Upload original file as-is (no resize / no quality loss)
                    FileUploader::upload($file, $filePath);

                    // Save to database
                    theme_setting::updateOrCreate(
                        ['type' => $key],
                        ['description' => $filePath]
                    );
                } else {
                    if (!empty($item) || $item === '0' || $item === 0) {
                        theme_setting::updateOrCreate(
                            ['type' => $key],
                            ['description' => $item]
                        );
                    }
                }
            }

            if ($request->filled('currency_code')) {
                $currencyCode = strtoupper((string) $request->input('currency_code'));
                if (! in_array($currencyCode, ['EGP', 'SAR', 'AED', 'USD', 'EUR'], true)) {
                    return redirect()->back()->withErrors(['currency_code' => get_phrase('العملة غير مدعومة')]);
                }

                Setting::updateOrCreate(
                    ['type' => 'system_currency'],
                    ['description' => $currencyCode]
                );
                clear_lms_cache('settings');
            }

            if ($request->filled('currency_position')) {
                Setting::updateOrCreate(
                    ['type' => 'currency_position'],
                    ['description' => (string) $request->input('currency_position')]
                );
                clear_lms_cache('settings');
            }

            clear_lms_cache('theme_settings');
            Session::flash('success', get_phrase('Theme setting updated successfully'));
            Session::flash('tab', 'general');
        }

        if ($param1 == 'theme_colors') {
            $palettes = theme_color_palettes();
            $allowedThemes = array_merge(array_keys($palettes), ['custom']);

            $validated = $request->validate([
                'color_theme' => 'required|string|in:' . implode(',', $allowedThemes),
                'color_accent' => ['required', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
                'color_accent_hover' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
                'color_primary' => ['required', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
                'color_secondary' => ['required', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
                'color_gray' => ['required', 'regex:/^#?[0-9A-Fa-f]{6}$/'],
            ]);

            $themeKey = $validated['color_theme'];

            if ($themeKey !== 'custom' && isset($palettes[$themeKey])) {
                $preset = $palettes[$themeKey];
                $colors = [
                    'color_theme' => $themeKey,
                    'color_accent' => normalize_hex_color($preset['accent']),
                    'color_accent_hover' => normalize_hex_color($preset['accent_hover'] ?? darken_hex_color($preset['accent'])),
                    'color_primary' => normalize_hex_color($preset['primary']),
                    'color_secondary' => normalize_hex_color($preset['secondary']),
                    'color_gray' => normalize_hex_color($preset['gray']),
                ];
            } else {
                $accent = normalize_hex_color($validated['color_accent']);
                $colors = [
                    'color_theme' => 'custom',
                    'color_accent' => $accent,
                    'color_accent_hover' => normalize_hex_color($validated['color_accent_hover'] ?? darken_hex_color($accent)),
                    'color_primary' => normalize_hex_color($validated['color_primary']),
                    'color_secondary' => normalize_hex_color($validated['color_secondary']),
                    'color_gray' => normalize_hex_color($validated['color_gray']),
                ];
            }

            foreach ($colors as $key => $value) {
                theme_setting::updateOrCreate(
                    ['type' => $key],
                    ['description' => $value]
                );
            }

            clear_lms_cache('theme_settings');
            Session::flash('success', get_phrase('تم تحديث ألوان الموقع بنجاح'));
            Session::flash('tab', 'colors');
        }

        if ($param1 == 'theme_about') {
            $allowed = ['about_subtitle', 'about_us', 'about_status'];
            foreach ($allowed as $key) {
                theme_setting::updateOrCreate(
                    ['type' => $key],
                    ['description' => $request->input($key) ?? '']
                );
            }
            clear_lms_cache('theme_settings');
            Session::flash('success', get_phrase('تم حفظ إعدادات صفحة من نحن بنجاح'));
            Session::flash('tab', 'about');
        }

        if ($param1 == 'theme_contact') {
            $allowed = ['contact_subtitle', 'contact_intro', 'contact_email', 'contact_phone', 'contact_address', 'contact_hours', 'contact_status'];
            foreach ($allowed as $key) {
                theme_setting::updateOrCreate(
                    ['type' => $key],
                    ['description' => $request->input($key) ?? '']
                );
            }
            clear_lms_cache('theme_settings');
            Session::flash('success', get_phrase('تم حفظ إعدادات صفحة التواصل بنجاح'));
            Session::flash('tab', 'contact');
        }

        if ($param1 == 'theme_accreditation') {
            $request->validate([
                'tvtc_logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:48048',
                'accreditation_document' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:48048',
            ]);

            $textFields = [
                'accreditation_status', 'accreditation_subtitle',
                'accreditation_body_title', 'accreditation_body_subtitle',
                'accreditation_description', 'accreditation_authority',
                'accreditation_number', 'accreditation_date',
                'accreditation_status_label', 'map_embed_url', 'map_link', 'map_address',
                // Ticker section settings (accreditations.blade.php)
                'accr_status', 'accr_eyebrow', 'accr_title', 'accr_desc',
            ];

            // Save badges JSON
            if ($request->has('accr_badges')) {
                $badgesRaw = $request->input('accr_badges');
                $badgesArr = json_decode($badgesRaw, true);
                if (is_array($badgesArr)) {
                    $cleanBadges = array_values(array_filter(array_map(function ($b) {
                        if (!is_array($b)) {
                            return null;
                        }
                        $name = substr(strip_tags((string) ($b['name'] ?? '')), 0, 100);
                        $sub  = substr(strip_tags((string) ($b['sub'] ?? '')), 0, 100);
                        if ($name === '' && $sub === '') {
                            return null;
                        }
                        return [
                            'name' => $name,
                            'sub'  => $sub,
                            'icon' => preg_replace('/[^a-z0-9\-]/', '', (string) ($b['icon'] ?? 'fa-award')),
                        ];
                    }, $badgesArr)));
                    theme_setting::updateOrCreate(
                        ['type' => 'accr_badges'],
                        ['description' => json_encode($cleanBadges, JSON_UNESCAPED_UNICODE)]
                    );
                }
            }

            // Save location info cards JSON
            if ($request->filled('loc_info_cards')) {
                $cardsRaw = $request->input('loc_info_cards');
                $cardsArr = json_decode($cardsRaw, true);
                if (is_array($cardsArr)) {
                    $cleanCards = array_values(array_filter(array_map(function ($c) {
                        if (!is_array($c)) return null;
                        $label = substr(strip_tags((string) ($c['label'] ?? '')), 0, 80);
                        $text  = substr(strip_tags((string) ($c['text'] ?? '')), 0, 300);
                        $icon  = preg_replace('/[^a-z0-9\-]/', '', (string) ($c['icon'] ?? 'fa-circle-info'));
                        if ($label === '' && $text === '') return null;
                        return compact('label', 'text', 'icon');
                    }, $cardsArr)));
                    theme_setting::updateOrCreate(
                        ['type' => 'loc_info_cards'],
                        ['description' => json_encode($cleanCards, JSON_UNESCAPED_UNICODE)]
                    );
                }
            }
            foreach ($textFields as $key) {
                theme_setting::updateOrCreate(
                    ['type' => $key],
                    ['description' => $request->input($key) ?? '']
                );
            }

            foreach (['tvtc_logo', 'accreditation_document'] as $key) {
                if ($request->hasFile($key)) {
                    $file     = $request->file($key);
                    $filePath = 'uploads/theme-thumbnail/' . nice_file_name($key, $file->extension());
                    FileUploader::upload($file, $filePath);
                    theme_setting::updateOrCreate(
                        ['type' => $key],
                        ['description' => $filePath]
                    );
                }
            }

            clear_lms_cache('theme_settings');
            Session::flash('success', get_phrase('تم حفظ إعدادات الاعتمادية بنجاح'));
            Session::flash('tab', 'accreditation');
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'msg'    => 'Theme setting updated successfully',
            ]);
        }

        return redirect()->back();
    }
  // start settings section

  // start social section

    public function social(){
        $social = theme_social::get();
        return view('theme::setting.social_media',compact('social'));

    }

    public function create_social(){
        return view('theme::setting.create_social');

    }
    public function social_store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'url' => 'required|url|max:255',
            'thumbnail' => 'required|string|max:100',
            'status' => 'nullable|in:0,1',
        ]);

        theme_social::create([
            'title' => $request->title,
            'url' => $request->url,
            'thumbnail' => $request->thumbnail, // Font Awesome brand slug (e.g. facebook)
            'status' => (int) ($request->status ?? 1),
        ]);

        return redirect()
            ->route('admin.theme.social')
            ->with('success', get_phrase('تم إضافة حساب التواصل بنجاح'));
    }

    public function social_delete($id)
    {
        $query = theme_social::where('id', $id)->first();

        if (! $query) {
            Session::flash('error', get_phrase('Data not found.'));

            return redirect()->back();
        }

        $query->delete();
        Session::flash('success', get_phrase('تم حذف حساب التواصل بنجاح'));

        return redirect()->back();
    }
   // end social section





    // start feature section
    public function feature(){
        $features = theme_feature::get();
        return view('theme::setting.feature',compact('features'));

    }

    public function create_feature(){
        return view('theme::setting.create_feature');

    }
    public function feature_store(Request $request){

        $validated = $request->validate([
            'title' => 'required|max:255',
            // 'color' => 'required|max:255',
            // 'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);


        $data['title'] = $request->title;
        $data['color'] = null;
        $data['status'] = $request->status;

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['thumbnail'] = null;
        // if(isset($request->thumbnail)){
        //     $data['thumbnail'] = "theme-thumbnail-thumbnail/" . nice_file_name($request->title, $request->thumbnail->extension());
        //     FileUploader::upload($request->thumbnail, $data['thumbnail'], 500, null, 200, 200);
        // }



        theme_feature::insert($data);

        return redirect(route('admin.theme.feature'))->with('success', get_phrase('feature added successfully'));

    }
    public function feature_delete($id){
        // check user data exists or not
        $query = theme_feature::where('id', $id)->first();
        if ($query->doesntExist()) {
            Session::flash('error', get_phrase('Data not found.'));
            return redirect()->back();
        }

        $query->delete();
        Session::flash('success', get_phrase('feature deleted successfully.'));
        return redirect()->back();
    }
   public function activeFeature($id)
    {
        $feature = theme_feature::where('id', $id)->first();
        if($feature->status == 1){

            $feature->update(['status' => 0]);
        }else{
            $feature->update(['status' => 1]);
        }


        Session::flash('success', get_phrase('feature updated successfully.'));
        return redirect()->back();    }
   // end feature section

    public function legal()
    {
        $page_data['terms'] = theme_legal_section::ofType('terms')->ordered()->get();
        $page_data['privacy'] = theme_legal_section::ofType('privacy')->ordered()->get();

        return view('theme::setting.legal', $page_data);
    }

    public function legal_store(Request $request)
    {
        $validated = $request->validate([
            'terms' => 'nullable|array',
            'terms.*.title' => 'required|string|max:255',
            'terms.*.body' => 'required|string',
            'privacy' => 'nullable|array',
            'privacy.*.title' => 'required|string|max:255',
            'privacy.*.body' => 'required|string',
        ], [
            'terms.*.title.required' => 'عنوان بند الشروط مطلوب.',
            'terms.*.body.required' => 'محتوى بند الشروط مطلوب.',
            'privacy.*.title.required' => 'عنوان بند الخصوصية مطلوب.',
            'privacy.*.body.required' => 'محتوى بند الخصوصية مطلوب.',
        ]);

        DB::transaction(function () use ($validated) {
            theme_legal_section::query()->delete();

            foreach (['terms', 'privacy'] as $type) {
                foreach ($validated[$type] ?? [] as $index => $row) {
                    theme_legal_section::create([
                        'type' => $type,
                        'title' => trim($row['title']),
                        'body' => trim($row['body']),
                        'sort_order' => $index + 1,
                        'status' => true,
                    ]);
                }
            }
        });

        Session::flash('success', get_phrase('تم حفظ الشروط وسياسة الخصوصية بنجاح'));

        return redirect()->route('admin.theme.legal');
    }
}
