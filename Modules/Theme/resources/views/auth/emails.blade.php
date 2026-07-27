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
                <p>اضغط على الزر التالي لإعادة تعيين كلمة المرور:</p>

                <p style="text-align:center; margin:30px 0;">
                    <a href="{{ $url }}"
                       style="background-color:#1bb1b2; color:#fff; text-decoration:none; padding:14px 28px; border-radius:8px; font-weight:bold; display:inline-block; transition: background-color 0.3s;"
                       onmouseover="this.style.backgroundColor='#169a9b'"
                       onmouseout="this.style.backgroundColor='#1bb1b2'">
                        إعادة تعيين الآن
                    </a>
                </p>

                <p>إذا لم تطلب ذلك، يمكنك تجاهل هذه الرسالة ولن يحدث أي تغيير لحسابك.</p>

                <p style="margin-top:30px; color:#777;">
                    تحياتنا ❤️<br>
                    <strong>فريق الدعم الفني</strong><br>
                    <span style="font-size:13px;">
                        البريد الإلكتروني: mohammedelbalshy4@gmail.com<br>
                       +رقم الهاتف: 01044445330
                    </span>
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
