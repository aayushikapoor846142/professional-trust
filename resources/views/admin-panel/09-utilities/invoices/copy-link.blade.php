
<div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xl-12">
                    <div class="multi-selectbox">
                       {{--<a href="{{$invoices->payment_link}}">{{$invoices->payment_link}}</a>--}}
                       <p class="mb-0 text-break"><a href="{{$url}}">{{$url}}</a></p>
                    </div>
                </div>  
        
            </div>
            <div class="text-end mt-4">
                <button id="copyBtn" data-id="{{$url}}" type="button" class="btn add-CdsTYButton-btn-primary">Copy</button>
                {{--<button id="copyBtn" data-id="{{$invoices->payment_link}}" type="button" class="btn add-CdsTYButton-btn-primary">Copy</button>--}}
            </div>
        </div>
    </div>
</div>
  
<script>
$(document).ready(function(){
    $("#copyBtn").click(function(){
        var copyText = $(this).attr('data-id');
        navigator.clipboard.writeText(copyText).then(function() {
            successMessage("Link Copied");
        }).catch(function(error) {
            errorMessage("Failed to copy: " + error);
        });
    });

})

</script>

