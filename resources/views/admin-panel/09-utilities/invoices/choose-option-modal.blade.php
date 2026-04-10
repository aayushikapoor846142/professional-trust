<div class="modal-dialog" style="max-width:unset;width:90%" >
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">{{ $pageTitle ?? '' }}</h5>
            <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                aria-label="Close">
                <i class="tio-clear tio-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="paymnet_link"></div>
            @if($invoice->currency == 'CAD')
                <button type="button" class="CdsTYButton-btn-primary payment_type" data-value="Stripe">Stripe</button>
            @else
                <button type="button" class="CdsTYButton-btn-primary payment_type" data-value="RazorPay">RazorPay</button>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-white btn-modal-close" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>
   
    $(document).on('click', '.payment_type', function () {
        var type_val = $(this).attr("data-value");
        $.ajax({
            type: "POST",
            url: BASEURL + '/invoices/generate-payment-link',
            data: {
                _token: csrf_token,
                type_val: type_val,
                invoice_id:"{{$invoice_id}}"
            },
            dataType: 'json',
            success: function(data) {
                if(data.status == true){
                    successMessage(data.message);
                    location.reload();
                }
            },
        });
        
    });
    $(document).on('click', '.btn-modal-close', function () {
        $('#popupModal').modal('hide');
    });
    
</script>
