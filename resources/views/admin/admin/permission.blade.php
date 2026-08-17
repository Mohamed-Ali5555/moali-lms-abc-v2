@extends('layouts.admin')
@push('title', get_phrase('Assign Permission'))

@php
    $sectionMeta = [
        'dashboard' => ['label' => 'لوحة التحكم', 'icon' => 'fi-rr-dashboard'],
        'bookstore' => ['label' => 'مكتبة الكتب', 'icon' => 'fi-rr-book'],
        'categories' => ['label' => 'التصنيفات', 'icon' => 'fi-rr-folder'],
        'sub_categories' => ['label' => 'التصنيفات الفرعية', 'icon' => 'fi-rr-folder-tree'],
        'wallet' => ['label' => 'المحفظة', 'icon' => 'fi-rr-wallet'],
        'wallet_category' => ['label' => 'تصنيفات المحفظة', 'icon' => 'fi-rr-tags'],
        'courses' => ['label' => 'الكورسات', 'icon' => 'fi-rr-graduation-cap'],
        'invoice' => ['label' => 'الفواتير', 'icon' => 'fi-rr-receipt'],
        'curriculum' => ['label' => 'المنهج الدراسي', 'icon' => 'fi-rr-list'],
        'admins' => ['label' => 'المشرفين', 'icon' => 'fi-rr-shield'],
        'student' => ['label' => 'الطلاب', 'icon' => 'fi-rr-users'],
        'instructor' => ['label' => 'المدرسين', 'icon' => 'fi-rr-user'],
        'bank_questions_category' => ['label' => 'تصنيفات بنك الأسئلة', 'icon' => 'fi-rr-folder'],
        'bank_quizs' => ['label' => 'اختبارات البنك', 'icon' => 'fi-rr-checkbox'],
        'bank_questions' => ['label' => 'بنك الأسئلة', 'icon' => 'fi-rr-comment-question'],
        'reports' => ['label' => 'التقارير', 'icon' => 'fi-rr-chart-histogram'],
        'newsletter' => ['label' => 'النشرة البريدية', 'icon' => 'fi-rr-envelope'],
        'blogs' => ['label' => 'المدونة', 'icon' => 'fi-rr-document'],
        'blog_categories' => ['label' => 'تصنيفات المدونة', 'icon' => 'fi-rr-apps'],
        'offline_payments' => ['label' => 'المدفوعات الأوفلاين', 'icon' => 'fi-rr-credit-card'],
        'coupons' => ['label' => 'الكوبونات', 'icon' => 'fi-rr-ticket'],
        'course_quiz' => ['label' => 'اختبارات الكورس', 'icon' => 'fi-rr-checkbox'],
        'exams_and_assinments' => ['label' => 'الامتحانات والواجبات', 'icon' => 'fi-rr-list'],
        'questions' => ['label' => 'أسئلة الكورس', 'icon' => 'fi-rr-interrogation'],
        'bootcamp_categories' => ['label' => 'تصنيفات البوتكامب', 'icon' => 'fi-rr-folder'],
        'bootcamps' => ['label' => 'البوتكامب', 'icon' => 'fi-rr-laptop'],
        'bootcamp_modules' => ['label' => 'وحدات البوتكامب', 'icon' => 'fi-rr-layers'],
        'bootcamp_live_classes' => ['label' => 'حصص البوتكامب المباشرة', 'icon' => 'fi-rr-video-camera'],
        'bootcamp_resources' => ['label' => 'موارد البوتكامب', 'icon' => 'fi-rr-cloud-download'],
        'team_training' => ['label' => 'التدريب الجماعي', 'icon' => 'fi-rr-users-alt'],
        'tutor_booking' => ['label' => 'حجز المدرسين', 'icon' => 'fi-rr-calendar'],
        'contact' => ['label' => 'رسائل التواصل', 'icon' => 'fi-rr-headset'],
        'messages' => ['label' => 'الرسائل', 'icon' => 'fi-rr-comment'],
        'api_configurations' => ['label' => 'إعدادات الـ API', 'icon' => 'fi-rr-settings-sliders'],
        'settings' => ['label' => 'الإعدادات', 'icon' => 'fi-rr-settings'],
        'live_classes' => ['label' => 'الحصص المباشرة', 'icon' => 'fi-rr-play'],
        'open_ai' => ['label' => 'الذكاء الاصطناعي', 'icon' => 'fi-rr-magic-wand'],
    ];

    $allRoutes = [
        'dashboard' => [
            'admin.dashboard.index' => 'عرض لوحة التحكم',
            'admin.dashboard' => 'الصفحة الرئيسية للوحة',
            'admin.phpinfo' => 'عرض معلومات PHP',
            'admin.revenue' => 'إيرادات الإدارة',
        ],
        'bookstore' => [
            'admin.bookstore' => 'عرض المكتبة',
            'admin.bookstore.create' => 'إضافة كتاب',
            'admin.bookstore.edit' => 'تعديل كتاب',
            'admin.bookstore.delete' => 'حذف كتاب',
            'admin.bookstore.activation' => 'تفعيل / إيقاف كتاب',
            'admin.bookstore.sort' => 'ترتيب الكتب',
        ],
        'categories' => [
            'admin.categories' => 'عرض التصنيفات',
            'admin.category.create' => 'إضافة تصنيف',
            'admin.category.edit' => 'تعديل تصنيف',
            'admin.category.transfer' => 'نقل التصنيف',
            'admin.category.delete' => 'حذف تصنيف',
        ],
        'sub_categories' => [
            'admin.sub_categories.create' => 'إضافة تصنيف فرعي',
            'admin.sub_categories.edit' => 'تعديل تصنيف فرعي',
            'admin.sub_categories.delete' => 'حذف تصنيف فرعي',
        ],
        'wallet' => [
            'admin.wallet.index' => 'عرض المحفظة',
            'admin.wallet.create' => 'إضافة رصيد',
            'admin.wallet.destroy' => 'حذف عملية',
            'admin.wallet.create_decreas' => 'خصم رصيد',
            'admin.wallet.export' => 'تصدير المحفظة',
            'admin.wallet.search' => 'بحث في المحفظة',
        ],
        'wallet_category' => [
            'admin.wallet_category.index' => 'عرض تصنيفات المحفظة',
            'admin.wallet_category.create' => 'إضافة تصنيف محفظة',
            'admin.wallet_category.destroy' => 'حذف تصنيف محفظة',
            'admin.wallet_category.search' => 'بحث تصنيفات المحفظة',
            'admin.wallet_category.export' => 'تصدير تصنيفات المحفظة',
        ],
        'courses' => [
            'admin.courses' => 'عرض الكورسات',
            'admin.course.create' => 'إضافة كورس',
            'admin.course.edit' => 'تعديل كورس',
            'admin.course.duplicate' => 'نسخ كورس',
            'admin.course.status' => 'تغيير حالة الكورس',
            'admin.course.delete' => 'حذف كورس',
            'admin.course.draft' => 'حفظ كمسودة',
            'admin.course.approval' => 'اعتماد الكورس',
            'admin.course.export' => 'تصدير الكورسات',
            'admin.course.search' => 'بحث الكورسات',
            'admin.course.filter' => 'فلترة الكورسات',
            'admin.course.view_on_front' => 'عرض الكورس في الموقع',
            'admin.course.course_playing' => 'تشغيل محتوى الكورس',
        ],
        'invoice' => [
            'admin.invoice' => 'عرض الفواتير',
        ],
        'curriculum' => [
            'admin.section.create' => 'إضافة قسم',
            'admin.section.edit' => 'تعديل قسم',
            'admin.section.delete' => 'حذف قسم',
            'admin.section.sort' => 'ترتيب الأقسام',
            'admin.lesson.create' => 'إضافة درس',
            'admin.lesson.edit' => 'تعديل درس',
            'admin.lesson.delete' => 'حذف درس',
            'admin.lesson.sort' => 'ترتيب الدروس',
            'admin.lesson.copy_move' => 'نسخ أو نقل درس',
            'admin.quiz.create' => 'إضافة اختبار للمنهج',
            'admin.quiz.choose' => 'اختيار اختبار',
            'admin.assingemnt.choose' => 'اختيار واجب',
            'admin.quiz_result.index' => 'عرض نتائج الاختبارات',
        ],
        'admins' => [
            'admin.admins.index' => 'عرض المشرفين',
            'admin.admins.create' => 'إضافة مشرف',
            'admin.admins.edit' => 'تعديل مشرف',
            'admin.admins.delete' => 'حذف مشرف',
            'admin.admins.permission' => 'إدارة صلاحيات المشرف',
            'admin.manage.profile' => 'إدارة الملف الشخصي',
        ],
        'student' => [
            'admin.student.index' => 'عرض الطلاب',
            'admin.student.create' => 'إضافة طالب',
            'admin.student.edit' => 'تعديل طالب',
            'admin.student.delete' => 'حذف طالب',
            'admin.student.view_course' => 'عرض كورسات الطالب',
            'admin.student.remove-device' => 'إزالة جهاز الطالب',
            'admin.enroll.history' => 'سجل الاشتراكات',
            'admin.enroll.history.delete' => 'حذف سجل اشتراك',
            'admin.enroll.history.edit' => 'تعديل تاريخ انتهاء الاشتراك',
            'admin.student.enroll' => 'تسجيل طالب في كورس',
            'admin.student.get' => 'جلب بيانات الطلاب',
            'admin.student.post' => 'حفظ بيانات الطلاب',
            'admin.student.not_enroll' => 'عرض غير المشتركين',
            'admin.enroll.history.search' => 'بحث سجل الاشتراكات',
            'admin.student.not_enroll.search' => 'بحث غير المشتركين',
            'admin.student.not_enroll.export' => 'تصدير غير المشتركين',
            'admin.student.search' => 'بحث الطلاب',
            'admin.student.export' => 'تصدير الطلاب',
        ],
        'instructor' => [
            'admin.instructor.index' => 'عرض المدرسين',
            'admin.instructor.create' => 'إضافة مدرس',
            'admin.instructor.edit' => 'تعديل مدرس',
            'admin.instructor.delete' => 'حذف مدرس',
            'admin.instructor.course' => 'عرض كورسات المدرس',
            'admin.instructor.payout' => 'عرض المدفوعات',
            'admin.instructor.payout.filter' => 'فلترة المدفوعات',
            'admin.instructor.payout.invoice' => 'فاتورة المدفوعات',
            'admin.instructor.payment' => 'دفع للمدرس',
            'admin.instructor.setting' => 'إعدادات المدرسين',
        ],
        'bank_questions_category' => [
            'admin.category.bank.questions.index' => 'عرض تصنيفات بنك الأسئلة',
            'admin.category.bank.questions.create' => 'إضافة تصنيف بنك أسئلة',
            'admin.category.bank.questions.edit' => 'تعديل تصنيف بنك أسئلة',
            'admin.category.bank.questions.delete' => 'حذف تصنيف بنك أسئلة',
        ],
        'bank_quizs' => [
            'admin.bank.quizs.index' => 'عرض اختبارات البنك',
            'admin.bank.quizs.create' => 'إضافة اختبار بنك',
            'admin.bank.quizs.edit' => 'تعديل اختبار بنك',
            'admin.bank.quizs.delete' => 'حذف اختبار بنك',
            'admin.bank.quizs.export' => 'تصدير اختبارات البنك',
            'admin.bank.quizs.search' => 'بحث اختبارات البنك',
            'admin.bank.quizs.filter' => 'فلترة اختبارات البنك',
            'admin.bank.quizs.show_question' => 'عرض أسئلة الاختبار',
        ],
        'bank_questions' => [
            'admin.bank.question.index' => 'عرض بنك الأسئلة',
            'admin.bank.question.edit' => 'تعديل سؤال',
            'admin.bank.question.delete' => 'حذف سؤال',
            'admin.bank.question.sort' => 'ترتيب الأسئلة',
            'admin.bank.question.type' => 'تحميل نوع السؤال',
            'admin.bank.quizs.using.category' => 'عرض الاختبارات حسب التصنيف',
            'admin.bank.question.search' => 'بحث بنك الأسئلة',
            'admin.bank.question.filter' => 'فلترة بنك الأسئلة',
            'admin.bank.question.export' => 'تصدير بنك الأسئلة',
        ],
        'reports' => [
            'admin.revenue' => 'عرض الإيرادات',
            'admin.revenue.delete' => 'حذف إيراد',
            'admin.revenue.export' => 'تصدير الإيرادات',
            'admin.revenue.search' => 'بحث الإيرادات',
            'admin.instructor.revenue' => 'إيرادات المدرسين',
            'admin.instructor_revenue.delete' => 'حذف إيراد مدرس',
            'admin.instructor.revenue.export' => 'تصدير إيرادات المدرسين',
            'admin.instructor.revenue.search' => 'بحث إيرادات المدرسين',
            'admin.purchase.history.course' => 'سجل شراء الكورسات',
            'admin.purchase.history.book' => 'سجل شراء الكتب',
            'admin.purchase.history.course.invoice' => 'فاتورة شراء كورس',
            'admin.purchase.history.book.invoice' => 'فاتورة شراء كتاب',
            'admin.purchase.history.course.search' => 'بحث سجل شراء الكورسات',
            'admin.purchase.history.book.search' => 'بحث سجل شراء الكتب',
            'admin.purchase.history.course.export' => 'تصدير سجل شراء الكورسات',
            'admin.purchase.history.book.export' => 'تصدير سجل شراء الكتب',
        ],
        'newsletter' => [
            'admin.newsletter' => 'عرض النشرات',
            'admin.newsletter.create' => 'إنشاء نشرة',
            'admin.newsletter.edit' => 'تعديل نشرة',
            'admin.newsletter.delete' => 'حذف نشرة',
            'admin.subscribed_user' => 'عرض المشتركين',
            'admin.subscribed_user.delete' => 'حذف مشترك',
            'admin.subscribed_user.export' => 'تصدير المشتركين',
            'admin.subscribed_user.search' => 'بحث المشتركين',
            'admin.newsletters.form' => 'نموذج النشرة',
            'admin.get.user' => 'جلب المستخدمين',
            'admin.send.newsletters' => 'إرسال النشرات',
        ],
        'blogs' => [
            'admin.blogs' => 'عرض المدونة',
            'admin.blog.create' => 'إضافة مقال',
            'admin.blog.edit' => 'تعديل مقال',
            'admin.blog.delete' => 'حذف مقال',
            'admin.blog.status' => 'تغيير حالة المقال',
            'admin.blog.pending' => 'المقالات المعلقة',
            'admin.blog.settings' => 'إعدادات المدونة',
            'admin.blog.export' => 'تصدير المقالات',
            'admin.blog.view_front' => 'عرض المقال في الموقع',
            'admin.blog.search' => 'بحث المقالات',
            'admin.blog.pending.search' => 'بحث المقالات المعلقة',
            'admin.blog.pending.export' => 'تصدير المقالات المعلقة',
        ],
        'blog_categories' => [
            'admin.blog.category' => 'عرض تصنيفات المدونة',
            'admin.blog.category.create' => 'إضافة تصنيف مدونة',
            'admin.blog.category.edit' => 'تعديل تصنيف مدونة',
            'admin.blog.category.delete' => 'حذف تصنيف مدونة',
        ],
        'offline_payments' => [
            'admin.offline.payments' => 'عرض المدفوعات الأوفلاين',
            'admin.offline.payment.doc' => 'تحميل المستند',
            'admin.offline.payment.accept' => 'قبول الدفع',
            'admin.offline.payment.decline' => 'رفض الدفع',
            'admin.offline.payment.delete' => 'حذف الدفع',
        ],
        'coupons' => [
            'admin.coupons' => 'عرض الكوبونات',
            'admin.coupon.create' => 'إضافة كوبون',
            'admin.coupon.edit' => 'تعديل كوبون',
            'admin.coupon.delete' => 'حذف كوبون',
            'admin.coupon.status' => 'تغيير حالة الكوبون',
            'admin.coupon.search' => 'بحث الكوبونات',
            'admin.coupon.export' => 'تصدير الكوبونات',
        ],
        'course_quiz' => [
            'admin.course.quiz.create' => 'إضافة اختبار كورس',
            'admin.course.quiz.edit' => 'تعديل اختبار كورس',
            'admin.course.quiz.delete' => 'حذف اختبار كورس',
            'admin.quiz.participant.result' => 'نتائج المشاركين',
            'admin.quiz.result.preview' => 'معاينة نتيجة الاختبار',
        ],
        'exams_and_assinments' => [
            'admin.exams.list' => 'عرض الامتحانات',
            'admin.exams.search' => 'بحث الامتحانات',
            'admin.exams.filter' => 'فلترة الامتحانات',
            'admin.exams.export' => 'تصدير الامتحانات',
            'admin.exams.activation' => 'تفعيل امتحان',
            'admin.exams.copy_move' => 'نسخ أو نقل امتحان',
            'admin.assignments.list' => 'عرض الواجبات',
            'admin.assignments.search' => 'بحث الواجبات',
            'admin.assignments.filter' => 'فلترة الواجبات',
            'admin.assignments.export' => 'تصدير الواجبات',
            'admin.assignments.activation' => 'تفعيل واجب',
        ],
        'questions' => [
            'admin.course.question.delete' => 'حذف سؤال الكورس',
            'admin.course.question.sort' => 'ترتيب أسئلة الكورس',
            'admin.load.question.type' => 'تحميل نوع السؤال',
            'admin.question.bank' => 'عرض بنك الأسئلة',
        ],
        'bootcamp_categories' => [
            'admin.bootcamp.categories' => 'عرض تصنيفات البوتكامب',
            'admin.bootcamp.category.create' => 'إضافة تصنيف بوتكامب',
            'admin.bootcamp.category.edit' => 'تعديل تصنيف بوتكامب',
            'admin.bootcamp.category.delete' => 'حذف تصنيف بوتكامب',
        ],
        'bootcamps' => [
            'admin.bootcamps' => 'عرض البوتكامب',
            'admin.bootcamp.create' => 'إضافة بوتكامب',
            'admin.bootcamp.edit' => 'تعديل بوتكامب',
            'admin.bootcamp.delete' => 'حذف بوتكامب',
            'admin.bootcamp.status' => 'تغيير حالة البوتكامب',
            'admin.bootcamp.duplicate' => 'نسخ بوتكامب',
            'admin.bootcamp.purchase.history' => 'سجل شراء البوتكامب',
            'admin.bootcamp.purchase.invoice' => 'فاتورة شراء البوتكامب',
            'admin.bootcamp.view_front' => 'عرض البوتكامب في الموقع',
            'admin.bootcamp.search' => 'بحث البوتكامب',
            'admin.bootcamp.filter' => 'فلترة البوتكامب',
            'admin.bootcamp.export' => 'تصدير البوتكامب',
        ],
        'bootcamp_modules' => [
            'admin.bootcamp.module.create' => 'إضافة وحدة',
            'admin.bootcamp.module.edit' => 'تعديل وحدة',
            'admin.bootcamp.module.delete' => 'حذف وحدة',
            'admin.bootcamp.module.sort' => 'ترتيب الوحدات',
        ],
        'bootcamp_live_classes' => [
            'admin.bootcamp.live.class.delete' => 'حذف حصة مباشرة',
            'admin.bootcamp.live.class.join' => 'الانضمام للحصة',
            'admin.bootcamp.class.end' => 'إنهاء الحصة',
            'admin.update.on.end.class' => 'تحديث بعد إنهاء الحصة',
        ],
        'bootcamp_resources' => [
            'admin.bootcamp.resource.delete' => 'حذف مورد',
            'admin.bootcamp.resource.download' => 'تحميل مورد',
        ],
        'team_training' => [
            'admin.team.packages' => 'عرض باقات الفريق',
            'admin.team.packages.create' => 'إضافة باقة فريق',
            'admin.team.packages.edit' => 'تعديل باقة فريق',
            'admin.team.packages.delete' => 'حذف باقة فريق',
            'admin.team.packages.duplicate' => 'نسخ باقة فريق',
            'admin.team.toggle.status' => 'تفعيل / إيقاف باقة',
            'admin.team.packages.purchase.history' => 'سجل شراء الباقات',
            'admin.team.packages.purchase.invoice' => 'فاتورة شراء الباقة',
            'admin.get.courses.by.privacy' => 'جلب الكورسات حسب الخصوصية',
            'admin.get.course.price' => 'جلب سعر الكورس',
        ],
        'tutor_booking' => [
            'admin.tutor_subjects' => 'عرض مواد المدرسين',
            'admin.tutor_subject_create' => 'إضافة مادة',
            'admin.tutor_subject_edit' => 'تعديل مادة',
            'admin.tutor_subject_status' => 'تغيير حالة المادة',
            'admin.tutor_subject_delete' => 'حذف مادة',
            'admin.tutor_categories' => 'عرض تصنيفات الحجز',
            'admin.tutor_category_create' => 'إضافة تصنيف حجز',
            'admin.tutor_category_edit' => 'تعديل تصنيف حجز',
            'admin.tutor_category_status' => 'تغيير حالة التصنيف',
            'admin.tutor_category_delete' => 'حذف تصنيف حجز',
        ],
        'contact' => [
            'admin.contacts' => 'عرض رسائل التواصل',
            'admin.contact.reply' => 'الرد على الرسالة',
            'admin.contact.delete' => 'حذف الرسالة',
            'admin.contact.search' => 'بحث الرسائل',
            'admin.contact.export' => 'تصدير الرسائل',
        ],
        'messages' => [
            'admin.message' => 'عرض الرسائل',
            'admin.message.create' => 'إنشاء رسالة',
            'admin.message.thread.store' => 'إنشاء محادثة',
        ],
        'api_configurations' => [
            'admin.api.configurations' => 'عرض إعدادات الـ API',
        ],
        'settings' => [
            'admin.system.settings' => 'إعدادات النظام',
            'admin.website.settings' => 'إعدادات الموقع',
            'admin.drip.settings' => 'إعدادات المحتوى المتدرج',
            'admin.payment.settings' => 'إعدادات الدفع',
            'admin.manage.language' => 'إدارة اللغات',
            'admin.language.delete' => 'حذف لغة',
            'admin.language.phrase.edit' => 'تعديل العبارات',
            'admin.language.phrase.import' => 'استيراد عبارات',
            'admin.language.phrase.create' => 'إضافة عبارة لغة',
            'admin.notification.settings' => 'إعدادات الإشعارات',
            'admin.player.settings' => 'إعدادات المشغّل',
            'admin.about' => 'صفحة حول النظام',
            'admin.certificate.settings' => 'إعدادات الشهادات',
            'admin.certificate.builder' => 'منشئ الشهادات',
            'admin.review.create' => 'إضافة تقييم',
            'admin.review.edit' => 'تعديل تقييم',
            'admin.review.delete' => 'حذف تقييم',
            'admin.update.home' => 'تحديث الصفحة الرئيسية',
            'admin.seo.settings' => 'إعدادات SEO',
            'admin.pages' => 'عرض الصفحات',
            'admin.theme.settings' => 'إعدادات الثيم',
            'admin.theme.settings.social' => 'إعدادات السوشيال',
            'admin.theme.settings.feature' => 'إعدادات مميزات الثيم',
            'admin.settings.smtp' => 'إعدادات SMTP',
            'admin.settings.home_page' => 'إعدادات الصفحة الرئيسية',
            'admin.wapilot.settings' => 'تكامل WaPilot',
        ],
        'live_classes' => [
            'admin.live.class.delete' => 'حذف حصة مباشرة',
            'admin.live.class.start' => 'بدء حصة مباشرة',
            'admin.live.class.settings' => 'إعدادات الحصص المباشرة',
            'admin.teams.settings' => 'إعدادات مايكروسوفت تيمز',
        ],
        'open_ai' => [
            'admin.open.ai.settings' => 'إعدادات OpenAI',
            'admin.open.ai.generate' => 'توليد محتوى بالذكاء الاصطناعي',
        ],
    ];

    $permission_row = DB::table('permissions')->where('admin_id', $admin->id)->first();
    $permissions = json_decode($permission_row->permissions ?? '[]', true);
    if (!is_array($permissions)) {
        $permissions = [];
    }

    $totalPermissions = 0;
    foreach ($allRoutes as $routes) {
        $totalPermissions += count($routes);
    }
    $enabledCount = 0;
    foreach ($allRoutes as $routes) {
        foreach (array_keys($routes) as $key) {
            if (in_array($key, $permissions, true)) {
                $enabledCount++;
            }
        }
    }
    $adminPhoto = !empty($admin->photo) ? get_image($admin->photo) : asset('assets/backend/images/default.jpg');
    $permissionPresets = get_admin_permission_presets();
@endphp

@section('content')
<style>
    .perm-page { direction: rtl; }

    .perm-hero {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        gap: 20px;
        padding: 22px 24px;
        border-radius: 20px;
        background:
            radial-gradient(ellipse at 0% 0%, rgba(13, 148, 136, 0.22), transparent 50%),
            linear-gradient(135deg, #0b1220 0%, #132033 100%);
        color: #e2e8f0;
        margin-bottom: 18px;
        overflow: hidden;
    }

    .perm-hero__eyebrow {
        margin: 0 0 6px;
        font-size: 12px;
        font-weight: 700;
        color: #5eead4;
        letter-spacing: .04em;
    }

    .perm-hero__title {
        margin: 0 0 8px;
        font-size: 24px;
        font-weight: 800;
        color: #f8fafc;
    }

    .perm-hero__desc {
        margin: 0;
        font-size: 14px;
        color: #94a3b8;
        line-height: 1.7;
        max-width: 560px;
    }

    .perm-hero__user {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 260px;
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(148,163,184,.18);
    }

    .perm-hero__user img {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        object-fit: cover;
        border: 2px solid rgba(94, 234, 212, .35);
    }

    .perm-hero__user strong {
        display: block;
        color: #f8fafc;
        font-size: 15px;
        margin-bottom: 2px;
    }

    .perm-hero__user span {
        font-size: 12px;
        color: #94a3b8;
    }

    .perm-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .perm-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 18px;
        box-shadow: 0 8px 24px rgba(15,23,42,.04);
    }

    .perm-stat strong {
        display: block;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .perm-stat span {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }

    .perm-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
        padding: 14px 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
    }

    .perm-search {
        position: relative;
        flex: 1;
        min-width: 220px;
        max-width: 420px;
    }

    .perm-search i {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .perm-search input {
        width: 100%;
        height: 44px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0 42px 0 14px;
        background: #f8fafc;
        font-weight: 600;
        color: #0f172a;
    }

    .perm-search input:focus {
        outline: none;
        border-color: #99f6e4;
        box-shadow: 0 0 0 3px rgba(13,148,136,.12);
        background: #fff;
    }

    .perm-toolbar__actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .perm-layout {
        display: grid;
        grid-template-columns: 240px minmax(0, 1fr);
        gap: 16px;
        align-items: start;
    }

    .perm-nav {
        position: sticky;
        top: 88px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 12px;
        max-height: calc(100vh - 110px);
        overflow: auto;
        box-shadow: 0 8px 24px rgba(15,23,42,.04);
    }

    .perm-nav__title {
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        letter-spacing: .06em;
        padding: 6px 10px 10px;
        margin: 0;
    }

    .perm-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 12px;
        color: #475569;
        text-decoration: none !important;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 2px;
        transition: .15s ease;
    }

    .perm-nav a i {
        width: 18px;
        color: #94a3b8;
        font-size: 14px;
    }

    .perm-nav a:hover,
    .perm-nav a.is-active {
        background: rgba(13,148,136,.1);
        color: #0f766e;
    }

    .perm-nav a:hover i,
    .perm-nav a.is-active i {
        color: #0d9488;
    }

    .perm-nav a .count {
        margin-inline-start: auto;
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 999px;
    }

    .perm-sections {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .perm-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(15,23,42,.04);
        scroll-margin-top: 96px;
    }

    .perm-card__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        background: linear-gradient(180deg, #f8fafc, #fff);
        border-bottom: 1px solid #e2e8f0;
    }

    .perm-card__meta {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .perm-card__icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(13,148,136,.12);
        color: #0f766e;
        font-size: 18px;
    }

    .perm-card__meta h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .perm-card__meta p {
        margin: 2px 0 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }

    .perm-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 10px;
        padding: 16px 18px 18px;
    }

    @media (min-width: 768px) {
        .perm-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (min-width: 1200px) {
        .perm-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    .perm-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        transition: .15s ease;
        cursor: pointer;
    }

    .perm-item:hover {
        border-color: #cbd5e1;
        background: #fff;
        box-shadow: 0 6px 16px rgba(15,23,42,.05);
    }

    .perm-item.is-on {
        border-color: #99f6e4;
        background: #f0fdfa;
    }

    .perm-item__text {
        min-width: 0;
    }

    .perm-item__text strong {
        display: block;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.4;
    }

    .perm-item__text small {
        display: block;
        margin-top: 3px;
        font-size: 10px;
        color: #94a3b8;
        font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }

    .perm-switch {
        position: relative;
        width: 46px;
        height: 26px;
        flex-shrink: 0;
    }

    .perm-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .perm-switch span {
        position: absolute;
        inset: 0;
        background: #cbd5e1;
        border-radius: 999px;
        cursor: pointer;
        transition: .2s ease;
    }

    .perm-switch span::before {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: .2s ease;
        box-shadow: 0 2px 6px rgba(15,23,42,.2);
    }

    .perm-switch input:checked + span {
        background: #0d9488;
    }

    .perm-switch input:checked + span::before {
        transform: translateX(20px);
    }

    .perm-empty {
        display: none;
        text-align: center;
        padding: 48px 20px;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        color: #64748b;
        font-weight: 700;
    }

    .perm-toast {
        position: fixed;
        bottom: 24px;
        left: 24px;
        z-index: 1080;
        padding: 12px 16px;
        border-radius: 12px;
        background: #0f172a;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 12px 30px rgba(15,23,42,.25);
        opacity: 0;
        transform: translateY(10px);
        pointer-events: none;
        transition: .25s ease;
    }

    .perm-toast.is-show {
        opacity: 1;
        transform: translateY(0);
    }

    .perm-presets {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .perm-preset {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 8px 24px rgba(15,23,42,.04);
    }

    .perm-preset strong {
        display: block;
        font-size: 14px;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .perm-preset p {
        margin: 0 0 12px;
        font-size: 12px;
        color: #64748b;
        line-height: 1.6;
    }

    @media (max-width: 991px) {
        .perm-layout { grid-template-columns: 1fr; }
        .perm-nav { position: static; max-height: none; display: flex; gap: 6px; overflow-x: auto; padding: 10px; }
        .perm-nav__title { display: none; }
        .perm-nav a { white-space: nowrap; margin: 0; }
        .perm-nav a .count { display: none; }
        .perm-hero { flex-direction: column; }
        .perm-hero__user { min-width: 0; width: 100%; }
        .perm-stats { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-page perm-page" data-admin-id="{{ $admin->id }}">
    <div class="perm-hero">
        <div>
            <p class="perm-hero__eyebrow">{{ get_phrase('Access Control') }}</p>
            <h1 class="perm-hero__title">{{ get_phrase('صلاحيات المشرف') }}</h1>
            <p class="perm-hero__desc">
                {{ get_phrase('فعّل أو أوقف أي صلاحية مباشرة — كل الأقسام ظاهرة ومترجمة، والتغيير يُحفظ فوراً') }}
            </p>
        </div>
        <div class="perm-hero__user">
            <img src="{{ $adminPhoto }}" alt="{{ $admin->name }}">
            <div>
                <strong>{{ $admin->name }}</strong>
                <span>{{ $admin->email }}</span>
            </div>
        </div>
    </div>

    <div class="perm-stats">
        <div class="perm-stat">
            <strong id="permEnabledCount">{{ $enabledCount }}</strong>
            <span>{{ get_phrase('صلاحيات مفعّلة') }}</span>
        </div>
        <div class="perm-stat">
            <strong>{{ $totalPermissions }}</strong>
            <span>{{ get_phrase('إجمالي الصلاحيات') }}</span>
        </div>
        <div class="perm-stat">
            <strong>{{ count($allRoutes) }}</strong>
            <span>{{ get_phrase('أقسام النظام') }}</span>
        </div>
    </div>

    <div class="perm-presets">
        @foreach ($permissionPresets as $presetKey => $preset)
            <div class="perm-preset">
                <strong>{{ get_phrase($preset['label']) }}</strong>
                <p>{{ get_phrase($preset['description']) }}</p>
                <button type="button" class="admin-btn admin-btn--primary w-100" onclick="applyPermissionPreset('{{ $presetKey }}')">
                    <i class="fi-rr-magic-wand"></i>
                    <span>{{ get_phrase('تطبيق القالب') }}</span>
                </button>
            </div>
        @endforeach
    </div>

    <div class="perm-toolbar">
        <div class="perm-search">
            <i class="fi-rr-search"></i>
            <input type="search" id="permSearch" placeholder="{{ get_phrase('ابحث عن صلاحية أو قسم...') }}" autocomplete="off">
        </div>
        <div class="perm-toolbar__actions">
            <button type="button" class="admin-btn admin-btn--primary" id="permExpandAll">
                <i class="fi-rr-eye"></i>
                <span>{{ get_phrase('إظهار الكل') }}</span>
            </button>
            <a href="{{ route('admin.admins.index') }}" class="admin-btn">
                <i class="fi-rr-arrow-alt-left"></i>
                <span>{{ get_phrase('رجوع') }}</span>
            </a>
        </div>
    </div>

    <div class="perm-layout">
        <aside class="perm-nav" id="permNav">
            <p class="perm-nav__title">{{ get_phrase('الأقسام') }}</p>
            @foreach ($allRoutes as $section => $routes)
                @php
                    $meta = $sectionMeta[$section] ?? ['label' => $section, 'icon' => 'fi-rr-apps'];
                    $sectionEnabled = 0;
                    foreach (array_keys($routes) as $key) {
                        if (in_array($key, $permissions, true)) {
                            $sectionEnabled++;
                        }
                    }
                @endphp
                <a href="#perm-{{ $section }}" data-section-link="{{ $section }}">
                    <i class="{{ $meta['icon'] }}"></i>
                    <span>{{ get_phrase($meta['label']) }}</span>
                    <span class="count" data-section-count="{{ $section }}">{{ $sectionEnabled }}/{{ count($routes) }}</span>
                </a>
            @endforeach
        </aside>

        <div class="perm-sections" id="permSections">
            @foreach ($allRoutes as $section => $routes)
                @php
                    $meta = $sectionMeta[$section] ?? ['label' => $section, 'icon' => 'fi-rr-apps'];
                    $sectionEnabled = 0;
                    foreach (array_keys($routes) as $key) {
                        if (in_array($key, $permissions, true)) {
                            $sectionEnabled++;
                        }
                    }
                @endphp
                <section class="perm-card" id="perm-{{ $section }}" data-section="{{ $section }}" data-section-label="{{ $meta['label'] }}">
                    <div class="perm-card__head">
                        <div class="perm-card__meta">
                            <span class="perm-card__icon"><i class="{{ $meta['icon'] }}"></i></span>
                            <div>
                                <h3>{{ get_phrase($meta['label']) }}</h3>
                                <p>
                                    <span data-section-enabled="{{ $section }}">{{ $sectionEnabled }}</span>
                                    /
                                    {{ count($routes) }}
                                    {{ get_phrase('صلاحية مفعّلة') }}
                                </p>
                            </div>
                        </div>
                        <button type="button" class="admin-btn admin-btn--primary" onclick="toggleAllPermissions('{{ $section }}', '{{ $admin->id }}')">
                            <i class="fi-rr-checkbox"></i>
                            <span>{{ get_phrase('تبديل الكل') }}</span>
                        </button>
                    </div>
                    <div class="perm-grid">
                        @foreach ($routes as $permission => $label)
                            @php $isOn = in_array($permission, $permissions, true); @endphp
                            <label class="perm-item {{ $isOn ? 'is-on' : '' }}" data-perm-item data-search="{{ $meta['label'] }} {{ $label }} {{ $permission }}">
                                <span class="perm-item__text">
                                    <strong>{{ get_phrase($label) }}</strong>
                                    <small title="{{ $permission }}">{{ $permission }}</small>
                                </span>
                                <span class="perm-switch">
                                    <input
                                        type="checkbox"
                                        class="permission-checkbox"
                                        id="perm-{{ md5($permission) }}"
                                        data-section="{{ $section }}"
                                        data-route-name="{{ $permission }}"
                                        onchange="setPermission('{{ $admin->id }}', this)"
                                        @if ($isOn) checked @endif
                                    >
                                    <span></span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div class="perm-empty" id="permEmpty">
                {{ get_phrase('لا توجد صلاحيات مطابقة لبحثك') }}
            </div>
        </div>
    </div>
</div>

<div class="perm-toast" id="permToast"></div>
@endsection

@push('js')
<script>
    "use strict";

    function showPermToast(msg) {
        const toast = document.getElementById('permToast');
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.add('is-show');
        clearTimeout(window.__permToastTimer);
        window.__permToastTimer = setTimeout(function () {
            toast.classList.remove('is-show');
        }, 1800);
    }

    function refreshPermCounters() {
        let enabledTotal = 0;
        document.querySelectorAll('.perm-card').forEach(function (card) {
            const section = card.getAttribute('data-section');
            const boxes = card.querySelectorAll('.permission-checkbox');
            const on = Array.from(boxes).filter(function (b) { return b.checked; }).length;
            enabledTotal += on;

            const enabledEl = document.querySelector('[data-section-enabled="' + section + '"]');
            if (enabledEl) enabledEl.textContent = on;

            const countEl = document.querySelector('[data-section-count="' + section + '"]');
            if (countEl) countEl.textContent = on + '/' + boxes.length;
        });
        const totalEl = document.getElementById('permEnabledCount');
        if (totalEl) totalEl.textContent = enabledTotal;
    }

    function setPermission(user_id, inputElement) {
        const permission = inputElement.getAttribute('data-route-name');
        const isChecked = inputElement.checked;
        const item = inputElement.closest('.perm-item');
        if (item) item.classList.toggle('is-on', isChecked);

        $.ajax({
            type: "post",
            url: "{{ route('admin.admins.permission.store') }}/" + user_id,
            data: {
                user_id: user_id,
                permission: permission,
                is_checked: isChecked,
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response == 1) {
                    refreshPermCounters();
                    showPermToast("{{ get_phrase('تم تحديث الصلاحية') }}");
                    if (typeof success === 'function') {
                        success("{{ get_phrase('تم تحديث الصلاحية') }}");
                    }
                }
            }
        });
    }

    function applyPermissionPreset(presetKey) {
        if (!confirm('{{ get_phrase('سيتم استبدال الصلاحيات الحالية بقالب جديد. هل تريد المتابعة؟') }}')) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: "{{ route('admin.admins.permission.preset', ['user_id' => $admin->id]) }}",
            data: {
                preset: presetKey,
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function (response) {
                if (!response || !response.success) {
                    showPermToast('{{ get_phrase('تعذر تطبيق القالب') }}');
                    return;
                }

                document.querySelectorAll('.permission-checkbox').forEach(function (checkbox) {
                    const routeName = checkbox.getAttribute('data-route-name');
                    const isOn = response.permissions.indexOf(routeName) !== -1;
                    checkbox.checked = isOn;
                    const item = checkbox.closest('.perm-item');
                    if (item) item.classList.toggle('is-on', isOn);
                });

                refreshPermCounters();
                showPermToast(response.message || '{{ get_phrase('تم تطبيق القالب') }}');
            },
            error: function () {
                showPermToast('{{ get_phrase('تعذر تطبيق القالب') }}');
            }
        });
    }

    function toggleAllPermissions(section, user_id) {
        const checkboxes = document.querySelectorAll('.permission-checkbox[data-section="' + section + '"]');
        const isAnyUnchecked = Array.from(checkboxes).some(function (checkbox) { return !checkbox.checked; });

        checkboxes.forEach(function (checkbox) {
            if (isAnyUnchecked) {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                    setPermission(user_id, checkbox);
                }
            } else {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    setPermission(user_id, checkbox);
                }
            }
        });
    }

    (function () {
        const searchInput = document.getElementById('permSearch');
        const emptyState = document.getElementById('permEmpty');
        const navLinks = document.querySelectorAll('[data-section-link]');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const q = (searchInput.value || '').trim().toLowerCase();
                let visibleCards = 0;

                document.querySelectorAll('.perm-card').forEach(function (card) {
                    let visibleItems = 0;
                    card.querySelectorAll('[data-perm-item]').forEach(function (item) {
                        const hay = (item.getAttribute('data-search') || '').toLowerCase();
                        const show = !q || hay.indexOf(q) !== -1;
                        item.style.display = show ? '' : 'none';
                        if (show) visibleItems++;
                    });
                    const showCard = visibleItems > 0;
                    card.style.display = showCard ? '' : 'none';
                    if (showCard) visibleCards++;

                    const section = card.getAttribute('data-section');
                    const link = document.querySelector('[data-section-link="' + section + '"]');
                    if (link) link.style.display = showCard ? '' : 'none';
                });

                if (emptyState) emptyState.style.display = visibleCards ? 'none' : 'block';
            });
        }

        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                navLinks.forEach(function (l) { l.classList.remove('is-active'); });
                link.classList.add('is-active');
            });
        });

        const expandBtn = document.getElementById('permExpandAll');
        if (expandBtn) {
            expandBtn.addEventListener('click', function () {
                document.querySelectorAll('.perm-card').forEach(function (card) {
                    card.style.display = '';
                    card.querySelectorAll('[data-perm-item]').forEach(function (item) {
                        item.style.display = '';
                    });
                });
                document.querySelectorAll('[data-section-link]').forEach(function (l) {
                    l.style.display = '';
                });
                if (searchInput) searchInput.value = '';
                if (emptyState) emptyState.style.display = 'none';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    })();
</script>
@endpush
