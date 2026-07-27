<script>
    $('.add-to-cart').on('click', function() {
        let id = $(this).attr('id-element');
        let type = $(this).attr('element-type');
        let url = "{{ route('theme.cart.store', ':id') }}".replace(':id', id);
        let cartNumber = +$('#cart-number').text();

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                id: id,
                _token: "{{ csrf_token() }}",
                type: type,
            },
            success: function(response) {
                if (response.result) {
                    $('#cart-number').text(cartNumber + 1);

                    Swal.fire({
                        icon: 'success',
                        title: 'تم الاضافه',
                        text: response.message
                    });

                    if (response.type === 'course' && response.is_free) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم التسجيل بنجاح',
                            text: 'تم تسجيلك في الكورس المجاني بنجاح!',
                            confirmButtonText: 'الذهاب للكورس'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/courses/' + response.course_id;
                            }
                        });
                    }

                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let message = 'حدث خطأ ما';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;

                    if (message.includes('يجب ان تسجل دخول')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'تسجيل الدخول مطلوب',
                            text: 'يجب عليك تسجيل الدخول أولا لشراء الكورس',
                            confirmButtonText: 'تسجيل الدخول'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route('theme.show_login') }}';
                            }
                        });
                        return;
                    }

                    if (message.includes('لقد قمت بالتسجيل')) {
                        Swal.fire({
                            icon: 'info',
                            title: 'تم التسجيل مسبقاً',
                            text: 'أنت مسجل بالفعل في هذا الكورس',
                            confirmButtonText: 'الذهاب للكورس'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/courses/' + id;
                            }
                        });
                        return;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: message
                });
            }
        });
    });
</script>

