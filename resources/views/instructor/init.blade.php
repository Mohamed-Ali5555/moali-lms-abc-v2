<script type="text/javascript">
    "use strict";

    window.lmsLoadAsset = (function() {
        var loaded = {};

        function loadCss(href) {
            if (loaded[href]) {
                return loaded[href];
            }
            loaded[href] = new Promise(function(resolve) {
                if (document.querySelector('link[href="' + href + '"]')) {
                    resolve();
                    return;
                }
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = href;
                link.onload = function() { resolve(); };
                link.onerror = function() { resolve(); };
                document.head.appendChild(link);
            });
            return loaded[href];
        }

        function loadScript(src) {
            if (loaded[src]) {
                return loaded[src];
            }
            loaded[src] = new Promise(function(resolve, reject) {
                if (document.querySelector('script[src="' + src + '"]')) {
                    resolve();
                    return;
                }
                var script = document.createElement('script');
                script.src = src;
                script.onload = function() { resolve(); };
                script.onerror = reject;
                document.body.appendChild(script);
            });
            return loaded[src];
        }

        return {
            css: loadCss,
            js: loadScript
        };
    })();

    $(function() {
        var asset = function(path) {
            return '{{ asset('') }}' + path.replace(/^\//, '');
        };

        // Date range picker
        if ($('.daterangepicker:not(.inited)').length) {
            lmsLoadAsset.css(asset('assets/backend/vendors/daterangepicker/daterangepicker.css'))
                .then(function() { return lmsLoadAsset.js(asset('assets/backend/vendors/daterangepicker/moment.min.js')); })
                .then(function() { return lmsLoadAsset.js(asset('assets/backend/vendors/daterangepicker/daterangepicker.js')); })
                .then(function() {
                    $('.daterangepicker:not(.inited)').daterangepicker();
                    $('.daterangepicker:not(.inited)').addClass('inited');
                });
        }

        // icon picker
        if ($('.icon-picker:not(.inited)').length) {
            Promise.all([
                lmsLoadAsset.css(asset('assets/global/icon-picker/fontawesome-iconpicker.min.css')),
                lmsLoadAsset.js(asset('assets/global/icon-picker/fontawesome-iconpicker.min.js'))
            ]).then(function() {
                $('.icon-picker:not(.inited)').iconpicker();
                $('.icon-picker:not(.inited)').addClass('inited');
            });
        }

        //Select 2
        if ($('#ajaxModal select.ol-select2:not(.inited)').length) {
            $('#ajaxModal select.ol-select2:not(.inited)').select2({
                dropdownParent: $('#ajaxModal')
            });
            $('#ajaxModal select.ol-select2:not(.inited)').addClass('inited');
        }
        if ($('#right-modal select.ol-select2:not(.inited)').length) {
            $('#right-modal select.ol-select2:not(.inited)').select2({
                dropdownParent: $('#right-modal')
            });
            $('#right-modal select.ol-select2:not(.inited)').addClass('inited');
        }
        if ($('select.ol-select2:not(.inited)').length) {
            $('select.ol-select2:not(.inited)').select2();
            $('select.ol-select2:not(.inited)').addClass('inited');
        }

        if ($('#ajaxModal select.select2:not(.inited)').length) {
            $('#ajaxModal select.select2:not(.inited)').select2({
                dropdownParent: $('#ajaxModal')
            });
            $('#ajaxModal select.select2:not(.inited)').addClass('inited');
        }
        if ($('#right-modal select.select2:not(.inited)').length) {
            $('#right-modal select.select2:not(.inited)').select2({
                dropdownParent: $('#right-modal')
            });
            $('#right-modal select.select2:not(.inited)').addClass('inited');
        }
        if ($('select.select2:not(.inited)').length) {
            $('select.select2:not(.inited)').select2();
            $('select.select2:not(.inited)').addClass('inited');
        }

        //Text editor
        if ($('.text_editor:not(.inited)').length) {
            Promise.all([
                lmsLoadAsset.css(asset('assets/global/summernote/summernote-lite.min.css')),
                lmsLoadAsset.js(asset('assets/global/summernote/summernote-lite.min.js'))
            ]).then(function() {
                $('.text_editor:not(.inited)').summernote({
                    height: 180,
                    minHeight: null,
                    maxHeight: null,
                    focus: true,
                });
                $('.text_editor:not(.inited)').addClass('inited');
            });
        }

        if ($('.tagify:not(.inited)').length) {
            Promise.all([
                lmsLoadAsset.css(asset('assets/global/tagify-master/dist/tagify.css')),
                lmsLoadAsset.js(asset('assets/global/tagify-master/dist/tagify.min.js'))
            ]).then(function() {
                $('.tagify:not(.inited)').each(function(index, element) {
                    new Tagify(element, {
                        placeholder: '{{ get_phrase('Enter your keywords') }}'
                    });
                    $(element).addClass('inited');
                });
            });
        }

        var formElement;
        if ($('.ajaxForm:not(.initialized)').length > 0) {
            $('.ajaxForm:not(.initialized)').ajaxForm({
                beforeSend: function(data, form) {
                    var formElement = $(form);
                },
                uploadProgress: function(event, position, total, percentComplete) {},
                complete: function(xhr) {

                    setTimeout(function() {
                        distributeServerResponse(xhr.responseText);
                    }, 400);

                    if ($('.ajaxForm.resetable').length > 0) {
                        $('.ajaxForm.resetable')[0].reset();
                    }
                },
                error: function(e) {
                    console.log(e);
                }
            });
            $('.ajaxForm:not(.initialized)').addClass('initialized');
        }
    });
</script>
