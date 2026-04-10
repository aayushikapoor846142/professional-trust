<div class="invite-users">
    <a href="javascript:;" class="search-icon"><i class="fa-sharp fa-regular fa-magnifying-glass"></i></a>
    <input type="text" id="search-users" placeholder="Find users and send invitation">
    <div class="users-search-result"></div>
</div>

@push("scripts")
<script>
    function sendInvitation(email){
        var url = "{{ baseUrl('connections/invitations/save') }}";
        $.ajax({
            url: url,
            type: "post",
            data: {
                _token:"{{csrf_token()}}",
                email:email
            },
            dataType: "json",
            beforeSend: function() {
                showLoader();
            },
            success: function(response) {
                hideLoader();
                if (response.status == true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
            error: function() {
                internalError();
            }
        });
    }
    $(document).ready(function(){
        $("#search-users").keyup(function(){
            var value = $(this).val();
            if(value.length > 3){
                $.ajax({
                    url: "{{ baseUrl('message-centre/search-users') }}",
                    dataType: "json",
                    data: {
                        value: value
                    },
                    dataType:"json",
                    beforeSend: function(){

                    },  
                    success: function(response) {
                        if(response.status){
                            $(".users-search-result").html(response.contents);
                        }else{
                            $(".users-search-result").html('');
                        }
                    }
                });
            }else{
                $(".users-search-result").html('');
            }
        });
        $(document).click(function (e) {
            if (!$(e.target).closest('.company-search-area').length) {
                $(".users-search-result").html('');
            }
        });

        // Prevent closing the dropdown when clicking inside
        $(".users-search-result").click(function (e) {
            e.stopPropagation();
        });
    })
</script>
@endpush
