<style>
    button#confirmBtn {
        background: var(--main-gradient);
        color: white;
        border: none;
        padding: 12px 50px;
        margin-top: 0;
        font-size: 18px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }
</style>
<div class="d-flex flex-column justify-content-center align-items-center gap-4 pt-4">
    <h4 >لتأكيد الدفع من خلال المحفظة برجاء الضغط علي موافق</h4>
    <form method="GET" action="{{route('payment.success',"Wallet")}}">
        <button id="confirmBtn">موافق</button>
    </form>
</div>
{{-- <script>
    $('#confirmBtn').on('click', function() {
        $.ajax({
            url: '{{route('payment.success',"Wallet")}}',
            method: 'GET',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#response').text('تم التأكيد بنجاح ✅');
            },
            error: function(xhr) {
                $('#response').text('حدث خطأ، حاول مرة أخرى ❌');
            }
        });
    });
</script> --}}
