<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            [
                'type' => 'language',
                'description' => 'english',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'system_name',
                'description' => 'Arkan',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'system_title',
                'description' => 'Arkan Learning Club',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'system_email',
                'description' => 'engalaamohamed0@gmail.com',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'address',
                'description' => 'ismailiya',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'phone',
                'description' => '201140404211',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'purchase_code',
                'description' => 'your-purchase-code',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'paypal',
                'description' => '[{"active":"1","mode":"sandbox","sandbox_client_id":"AfGaziKslex-scLAyYdDYXNFaz2aL5qGau-SbDgE_D2E80D3AFauLagP8e0kCq9au7W4IasmFbirUUYc","sandbox_secret_key":"EMa5pCTuOpmHkhHaCGibGhVUcKg0yt5-C3CzJw-OWJCzaXXzTlyD17SICob_BkfM_0Nlk7TWnN42cbGz","production_client_id":"1234","production_secret_key":"12345"}]',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'stripe_keys',
                'description' => '[{"active":"1","testmode":"on","public_key":"pk_test_CAC3cB1mhgkJqXtypYBTGb4f","secret_key":"sk_test_iatnshcHhQVRXdygXw3L2Pp2","public_live_key":"pk_live_xxxxxxxxxxxxxxxxxxxxxxxx","secret_live_key":"sk_live_xxxxxxxxxxxxxxxxxxxxxxxx"}]',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'youtube_api_key',
                'description' => 'youtube-and-google-drive-api-key',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'vimeo_api_key',
                'description' => 'vimeo-api-key',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'slogan',
                'description' => 'A course based video CMS',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'text_align',
                'description' => null,
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'allow_instructor',
                'description' => '1',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-12-05 23:04:06'
            ],
            [
                'type' => 'instructor_revenue',
                'description' => '70',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-12-05 23:04:11'
            ],
            [
                'type' => 'system_currency',
                'description' => 'USD',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-02-20 18:02:55'
            ],
            [
                'type' => 'paypal_currency',
                'description' => 'USD',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'stripe_currency',
                'description' => 'USD',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'author',
                'description' => 'Alaa Abdelltife',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'currency_position',
                'description' => 'right-space',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-02-20 18:02:55'
            ],
            [
                'type' => 'website_description',
                'description' => null,
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'website_keywords',
                'description' => 'LMS,Learning Management System,Creativeitem,Academy LMS',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'footer_text',
                'description' => 'Creativeitem',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'footer_link',
                'description' => 'https://alaa-abdelltif.com/',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'protocol',
                'description' => 'smtp',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2024-09-24 06:07:39'
            ],
            [
                'type' => 'smtp_host',
                'description' => 'smtp.gmail.com',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2024-09-24 06:07:39'
            ],
            [
                'type' => 'smtp_port',
                'description' => '465',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2024-09-24 06:07:39'
            ],
            [
                'type' => 'smtp_user',
                'description' => 'your-email-address',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2024-09-24 06:07:39'
            ],
            [
                'type' => 'smtp_pass',
                'description' => 'enter-your-smtp-password',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2024-09-24 06:07:39'
            ],
            [
                'type' => 'version',
                'description' => '1.5.1',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'student_email_verification',
                'description' => '0',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'instructor_application_note',
                'description' => 'Fill all the fields carefully and share if you want to share any document with us it will help us to evaluate you as an instructor. dfdfs',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-12-05 23:04:06'
            ],
            [
                'type' => 'razorpay_keys',
                'description' => '[{"active":"1","key":"rzp_test_J60bqBOi1z1aF5","secret_key":"uk935K7p4j96UCJgHK8kAU4q","theme_color":"#c7a600"}]',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'razorpay_currency',
                'description' => 'USD',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'fb_app_id',
                'description' => 'fb-app-id',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'fb_app_secret',
                'description' => 'fb-app-secret',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'fb_social_login',
                'description' => '0',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'drip_content_settings',
                'description' => '{"lesson_completion_role":"duration","minimum_duration":"15:30:00","minimum_percentage":"60","locked_lesson_message":"<h3 xss=\"removed\" style=\"text-align: center; \"><span xss=\"removed\" style=\"\">Permission denied!</span></h3><p xss=\"removed\" style=\"text-align: center; \"><span xss=\"removed\">This course supports drip content, so you must complete the previous lessons.</span></p>","files":null}',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:26:38'
            ],
            [
                'type' => 'course_accessibility',
                'description' => 'publicly',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-12-05 00:54:45'
            ],
            [
                'type' => 'smtp_crypto',
                'description' => 'ssl',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2024-09-24 06:07:39'
            ],
            [
                'type' => 'academy_cloud_access_token',
                'description' => 'jdfghasdfasdfasdfasdfasdf',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'course_selling_tax',
                'description' => '0',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ],
            [
                'type' => 'ccavenue_keys',
                'description' => '[{"active":"1","ccavenue_merchant_id":"cmi_xxxxxx","ccavenue_working_key":"cwk_xxxxxxxxxxxx","ccavenue_access_code":"ccc_xxxxxxxxxxxxx"}]',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'ccavenue_currency',
                'description' => 'INR',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'iyzico_keys',
                'description' => '[{"active":"1","testmode":"on","iyzico_currency":"TRY","api_test_key":"atk_xxxxxxxx","secret_test_key":"stk_xxxxxxxx","api_live_key":"alk_xxxxxxxx","secret_live_key":"slk_xxxxxxxx"}]',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'iyzico_currency',
                'description' => 'TRY',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'paystack_keys',
                'description' => '[{"active":"1","testmode":"on","secret_test_key":"sk_test_c746060e693dd50c6f397dffc6c3b2f655217c94","public_test_key":"pk_test_0816abbed3c339b8473ff22f970c7da1c78cbe1b","secret_live_key":"sk_live_xxxxxxxxxxxxxxxxxxxxx","public_live_key":"pk_live_xxxxxxxxxxxxxxxxxxxxx"}]',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'paystack_currency',
                'description' => 'NGN',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'paytm_keys',
                'description' => '[{"PAYTM_MERCHANT_KEY":"PAYTM_MERCHANT_KEY","PAYTM_MERCHANT_MID":"PAYTM_MERCHANT_MID","PAYTM_MERCHANT_WEBSITE":"DEFAULT","INDUSTRY_TYPE_ID":"Retail","CHANNEL_ID":"WEB"}]',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'google_analytics_id',
                'description' => null,
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-12-05 00:54:45'
            ],
            [
                'type' => 'meta_pixel_id',
                'description' => null,
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-12-05 00:54:45'
            ],
            [
                'type' => 'smtp_from_email',
                'description' => 'your-email-address',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2024-09-24 06:07:39'
            ],
            [
                'type' => 'language_dirs',
                'description' => '{"english":"ltr","hindi":"rtl","arabic":"rtl"}',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2023-10-29 05:36:40'
            ],
            [
                'type' => 'certificate_template',
                'description' => 'uploads/certificate-template/certificate-default.png',
                'created_at' => '2024-03-12 08:17:10',
                'updated_at' => '2024-08-27 05:21:49'
            ],
            [
                'type' => 'certificate_builder_content',
                'description' => '<style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style>


                                <style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style>


                                <style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style>


                                <style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style>



<style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><style>
            @import url(\'https://fonts.googleapis.com/css2?family=Italianno&display=swap\');
            @import url(\'https://fonts.googleapis.com/css2?family=Pinyon+Script&display=swap%27\');
            @import url(\'https://fonts.googleapis.com/css2?family=Miss+Fajardose&display=swap%27\');
        </style><div id="certificate-layout-module" class="certificate-layout-module resizeable-canvas draggable ui-draggable ui-draggable-handle ui-resizable hidden-position" style="position: relative; width: 1069.2px; height: 755.055px; left: 0px; top: -1px;" bis_skin_checked="1">
                <img class="certificate-template" style="width: 100%; height: 100%;" src="http://localhost/academy-laravel/academy_1.4/public/uploads/certificate-template/certificate-default.png"><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute; font-size: 16px; top: 114px; left: 93px; width: 84.8906px; font-family: "auto"; padding: 5px !important; height: 80px;" bis_skin_checked="1">
                {qr_code}
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute; font-size: 18px; top: 546px; left: 125px; width: 210.031px; font-family: "Pinyon Script"; padding: 5px !important; height: 37px;" bis_skin_checked="1">
                {instructor_name}
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute; font-size: 18px; top: 546px; left: 724px; width: 210.188px; font-family: "Pinyon Script"; padding: 5px !important; height: 39px;" bis_skin_checked="1">
                {student_name}
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute; font-size: 16px; top: 545px; left: 442px; width: min-content; font-family: "auto"; padding: 5px !important;" bis_skin_checked="1">
                {course_completion_date}
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute; font-size: 12px; top: 665px; left: 457px; width: min-content; font-family: "auto"; padding: 5px !important;" bis_skin_checked="1">
                {certificate_download_date}
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute;font-size: 30px;top: 136px;left: 264px;width: 534.336px;padding: 5px !important;height: 62px;font-family: auto;" bis_skin_checked="1">
                COURSE COMPLETION CERTIFICATE
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute; font-size: 18px; top: 211px; left: 205px; width: 664.5px; font-family: "Pinyon Script"; padding: 5px !important; height: 98px;" bis_skin_checked="1">
                This certificate is awarded to {student_name} in recognition of their successful completion of Course on {course_completion_date}. Your hard work, dedication, and commitment to learning have enabled you to achieve this milestone, and we are proud to recognize your accomplishment.
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div><div class="draggable resizeable-canvas ui-draggable ui-draggable-handle ui-resizable" style="position: absolute; font-size: 18px; top: 316px; left: 315px; width: 428.25px; font-family: "auto"; padding: 5px !important; height: 48px;" bis_skin_checked="1">
                {course_title}
                <i class="remove-item fi-rr-cross-circle cursor-pointer" onclick="$(this).parent().remove()">
            </i><div class="ui-resizable-handle ui-resizable-e" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-s" style="z-index: 90;" bis_skin_checked="1"></div><div class="ui-resizable-handle ui-resizable-se ui-icon ui-icon-gripsmall-diagonal-se" style="z-index: 90;" bis_skin_checked="1"></div></div></div>',
                'created_at' => '2024-03-12 08:17:50',
                'updated_at' => '2024-10-29 01:43:51'
            ],
            [
                'type' => '_token',
                'description' => 'vUQNy3J0jlyS80NmKffH7nD9PMiZylyqHXmKm3Fv',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:10'
            ],
            [
                'type' => 'zoom_account_email',
                'description' => 'mohammedelbalshy4work@gmail.com',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:11'
            ],
            [
                'type' => 'zoom_account_id',
                'description' => 'vpBHTTR2Tv-OnS_guDG-5A',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:11'
            ],
            [
                'type' => 'zoom_client_id',
                'description' => 'AvrMsZP5SYHgzx8gTOo1w',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:11'
            ],
            [
                'type' => 'zoom_client_secret',
                'description' => '7ksi9uimS154pygGhudTZN3iGKp1Ruue',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:11'
            ],
            [
                'type' => 'zoom_web_sdk',
                'description' => 'inactive',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:11'
            ],
            [
                'type' => 'zoom_sdk_client_id',
                'description' => '7M6Wxxxxxxxxxxxx',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:11'
            ],
            [
                'type' => 'zoom_sdk_client_secret',
                'description' => 'z1Nzxxxxxxxxxxxxxx',
                'created_at' => '2024-03-12 08:18:24',
                'updated_at' => '2025-11-24 14:30:11'
            ],
            [
                'type' => 'open_ai_model',
                'description' => 'gpt-3.5-turbo-0125',
                'created_at' => '2024-03-12 09:11:12',
                'updated_at' => '2024-08-27 05:25:46'
            ],
            [
                'type' => 'open_ai_max_token',
                'description' => '100',
                'created_at' => '2024-03-12 09:11:12',
                'updated_at' => '2024-08-27 05:25:46'
            ],
            [
                'type' => 'open_ai_secret_key',
                'description' => 'sk-JPYxxxxxxxxxxxxxxxxxxx',
                'created_at' => '2024-03-12 09:11:12',
                'updated_at' => '2024-08-27 05:25:46'
            ],
            [
                'type' => 'timezone',
                'description' => 'Africa/Cairo',
                'created_at' => '2024-07-01 02:06:24',
                'updated_at' => '2024-07-01 08:06:24'
            ],
            [
                'type' => 'device_limitation',
                'description' => '10',
                'created_at' => '2023-10-29 05:36:40',
                'updated_at' => '2025-03-14 21:42:00'
            ]
        ];

        DB::table('settings')->insert($settings);
    }
}
