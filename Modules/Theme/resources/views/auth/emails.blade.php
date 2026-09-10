<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعادة تعيين كلمة المرور</title>
</head>
<body style="font-family: 'Tahoma', sans-serif; background-color: #f6f8fa; padding: 40px 20px;">
    <table width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
        <tr>
            <td style="background-color:#1bb1b2; color:#fff; text-align:center; padding:20px 0; font-size:22px; font-weight:bold;">
                🔐 إعادة تعيين كلمة المرور
            </td>
        </tr>
        <tr>
            <td style="padding:30px; text-align:right; color:#333; line-height:1.6;">
                <p>مرحباً <strong>{{ $user->name ?? 'عزيزي المستخدم' }}</strong>،</p>
                <p>لقد طلبت إعادة تعيين كلمة المرور لحسابك لدينا.</p>

                <p style="margin-top: 20px; font-weight: bold;">اتبع الخطوات التالية:</p>
                <ol style="padding-right: 20px; margin: 10px 0 20px; line-height: 1.8;">
                    <li>اضغط على زر «إعادة تعيين الآن» أدناه.</li>
                    <li>ستُفتح صفحة لتعيين كلمة مرور جديدة.</li>
                    <li>أدخل كلمة المرور الجديدة مرتين للتأكيد.</li>
                    <li>بعد الحفظ، سجّل الدخول بكلمة المرور الجديدة.</li>
                </ol>

                <p style="text-align:center; margin:30px 0;">
                    <a href="{{ $url }}"
                       style="background-color:#1bb1b2; color:#fff; text-decoration:none; padding:14px 28px; border-radius:8px; font-weight:bold; display:inline-block;">
                        إعادة تعيين الآن
                    </a>
                </p>

                <p style="font-size: 13px; color: #666; background: #f9f9f9; padding: 12px; border-radius: 8px;">
                    <strong>ملاحظة:</strong> هذا الرابط صالح لمدة <strong>60 دقيقة</strong> فقط. إذا انتهت صلاحيته، اطلب رابطاً جديداً من صفحة «نسيت كلمة المرور».
                </p>

                <p>إذا لم تطلب ذلك، يمكنك تجاهل هذه الرسالة ولن يحدث أي تغيير لحسابك.</p>

                <p style="margin-top:30px; color:#777;">
                    تحياتنا ❤️<br>
                    <strong>فريق الدعم الفني</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color:#f0f0f0; text-align:center; padding:20px 15px; font-size:12px; color:#666;">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="padding-bottom:10px;">
                            <strong>تاريخ الإرسال:</strong> {{ date('Y-m-d H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#999;">
                            © {{ date('Y') }} جميع الحقوق محفوظة.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
