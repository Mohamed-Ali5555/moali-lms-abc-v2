
    @php
        // جلب بيانات الطالب
        $studentName = auth()->check() ? auth()->user()->name : 'Guest';
        $studentPhone = auth()->check() ? auth()->user()->phone : '';
        $watermarkText = $studentName . " " . $studentPhone;
    @endphp

    <style>
        .all-content-protection-container {
            position: relative;
            width: 100%;
            min-height: 300px;
            background: #fff;
            overflow: hidden;
        }

        .watermark-overlay {
            position: absolute;
            color: rgba(0, 39, 155, 0.478);
            font-size: 18px;
            font-weight: bold;
            z-index: 2147483647 !important;
            pointer-events: none;
            user-select: none;
            text-shadow: 0.5px 0.5px 1px rgba(255,255,255,0.5);
            white-space: nowrap;
            display: block !important;
            color: rgba(200, 200, 200, 0.5) !important;

            text-shadow:
                -1px -1px 0 #000,
                1px -1px 0 #000,
                -1px  1px 0 #000,
                1px  1px 0 #000;

            font-size: 18px;
            font-weight: bold;
        }

          .video-parent-container {
            position: relative;
            overflow: hidden;
        }


        .watermark-container {


            opacity: 1 !important;

        }
    </style>
    @if (isset($lesson_details->lesson_type))



    <div class="all-content-protection-container" id="protection-container">

        <div id="dynamic-watermark" class="watermark-overlay">
            {{ $watermarkText }}
        </div>

        @if ($lesson_details->lesson_type == 'text')
            <div class="course-video-area border-primary p-4">
                <div class="text_show">
                    {!! removeScripts($lesson_details->attachment) !!}
                </div>
            </div>

        @elseif (in_array($lesson_details->lesson_type, ['video-url', 'system-video', 'vimeo-url', 'google_drive', 'html5']))
            <div class="course-video-area border-primary border">
                <div class="course-video-wrap">
                    @if ($lesson_details->lesson_type == 'video-url')
                        <div id="player">
                            <iframe src="{{ $lesson_details->lesson_src }}?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1" allowfullscreen allowtransparency allow="autoplay"></iframe>
                        </div>
                    @elseif($lesson_details->lesson_type == 'system-video')
                        <video id="player" playsinline controls oncontextmenu="return false;" width="100%">
                            <source src="{{ route('course.get_file', ['course_id' => $lesson_details->course_id, 'lesson_id' => $lesson_details->id]) }}" type="video/mp4">
                        </video>
                    @elseif($lesson_details->lesson_type == 'vimeo-url')
                        <iframe height="500" width="100%" src="https://player.vimeo.com/video/{{ str_replace('https://vimeo.com/', '', $lesson_details->lesson_src) }}" allowfullscreen></iframe>
                    @elseif($lesson_details->lesson_type == 'google_drive')
                        @php preg_match('/\/d\/(.*?)\//', $lesson_details->lesson_src, $matches); @endphp
                        <iframe width="100%" height="500px" src="https://drive.google.com/file/d/{{ $matches[1] ?? '' }}/preview" allowfullscreen></iframe>
                    @elseif($lesson_details->lesson_type == 'html5')
                        <video width="100%" height="680" id="player" playsinline controls>
                            <source src="{{ $lesson_details->lesson_src }}" type="video/mp4">
                        </video>
                    @endif
                    @include('course_player.player_config')
                </div>
            </div>

        @elseif($lesson_details->lesson_type == 'image')
            <div class="p-2 text-center">
                <img width="100%" src="{{ route('course.get_file', ['course_id' => $lesson_details->course_id, 'lesson_id' => $lesson_details->id]) }}" />
            </div>

        @elseif($lesson_details->lesson_type == 'document_type')
             <div style="position:relative;">
                @if ($lesson_details->attachment_type == 'pdf')
                    <iframe width="100%" height="600px" src="{{ route('pdf_canvas', ['course_id' => $lesson_details->course_id, 'lesson_id' => $lesson_details->id]) }}" allowfullscreen></iframe>
                @else
                    <iframe width="100%" height="500px" src="{{ route('course.get_file', ['course_id' => $lesson_details->course_id, 'lesson_id' => $lesson_details->id]) }}"></iframe>
                @endif
             </div>

        @elseif($lesson_details->lesson_type == 'quiz')
            <div class="course-video-area border-primary pb-5" dir="rtl">
                @include('course_player.quiz.index')
            </div>
        @endif

    </div>
@endif

<script>
    (function() {
        const watermark = document.getElementById('dynamic-watermark');
        const container = document.getElementById('protection-container');

        if (!watermark || !container) return;

        // دالة التحريك الذكي
        function moveWatermark() {
            // التحقق من وضع ملء الشاشة (Full Screen)
            const fsElem = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;

            let targetParent = container;
            if (fsElem) {
                // إذا الطالب كبر الشاشة، انقل العلامة المائية داخل العنصر المكبر فوراً
                if (watermark.parentElement !== fsElem) {
                    fsElem.appendChild(watermark);
                }
                targetParent = fsElem;
            } else {
                // رجوع العلامة للحاوية الأصلية
                if (watermark.parentElement !== container) {
                    container.appendChild(watermark);
                }
            }

            const maxX = targetParent.clientWidth - watermark.offsetWidth - 50;
            const maxY = targetParent.clientHeight - watermark.offsetHeight - 50;

            const x = Math.max(20, Math.floor(Math.random() * maxX));
            const y = Math.max(20, Math.floor(Math.random() * maxY));

            watermark.style.left = x + 'px';
            watermark.style.top = y + 'px';
        }

        moveWatermark();
        setInterval(moveWatermark, 4000); // تحريك كل 4 ثواني

        // مراقبة تغيير وضع الشاشة
        ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach(event => {
            document.addEventListener(event, moveWatermark);
        });

        // مراقبة الحذف أو التلاعب - مُحسّنة لتجنب تعليق المتصفح
        // المشكلة السابقة: مراقبة document.body بالكامل كانت تسبب استدعاءات كثيرة جداً
        // خاصة أثناء الامتحانات (كل نقرة، كل تغيير سؤال) مما يؤدي لتعليق/إغلاق المتصفح
        var checkScheduled = false;
        function checkWatermarkIntegrity() {
            checkScheduled = false;
            try {
                if (!document.body.contains(watermark)) {
                    container.innerHTML = "<h1 style='color:red; text-align:center; margin-top:100px;'>محاولة اختراق مكتشفة!</h1>";
                    setTimeout(function() { location.reload(); }, 2000);
                    return;
                }
                var style = window.getComputedStyle(watermark);
                if (style.display === 'none' || style.visibility === 'hidden') {
                    container.innerHTML = "<h1 style='color:red; text-align:center; margin-top:100px;'>محاولة اختراق مكتشفة!</h1>";
                    setTimeout(function() { location.reload(); }, 2000);
                }
            } catch (e) { /* تجاهل أخطاء الوصول للعناصر */ }
        }
        var observer = new MutationObserver(function() {
            if (checkScheduled) return;
            checkScheduled = true;
            setTimeout(checkWatermarkIntegrity, 150);
        });
        // مراقبة الحاوية فقط (وليس الصفحة بأكملها) - يقلل الاستدعاءات بشكل كبير
        observer.observe(container, { attributes: true, childList: true, subtree: true });

        // منع الزر الأيمن على الحاوية بالكامل
        container.oncontextmenu = function() { return false; };
    })();
</script>





<script>
    // Disable right-click on video
    if(document.getElementById('player')) {
        document.getElementById('player').oncontextmenu = function() {
            return false; // Prevent right-click menu
        };
    }
    if(document.getElementById('google-drive-player')) {
        document.getElementById('google-drive-player').oncontextmenu = function() {
            return false; // Prevent right-click menu
        };
    }
</script>
