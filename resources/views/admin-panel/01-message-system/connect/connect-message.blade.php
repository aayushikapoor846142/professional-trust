
@extends('admin-panel.layouts.app')
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
@if(!$canAddConnection)
    <div class="container-fluid">
        <div class="alert alert-danger mb-3">
            {{ $connectionFeatureStatus['message'] }}
        </div>
    </div>
    @else
        <div class="alert alert-warning mb-3">
            <strong>⚠ Connection Management</strong><br>
            {{ $connectionFeatureStatus['message'] }}
        </div>
    @endif
 <div class="invite-users">
            <a href="javascript:;" class="search-icon"><i class="fa-magnifying-glass fa-regular fa-sharp"></i></a>
            <input type="text" id="search-users" placeholder="Find users and send invitation" />
            <div class="users-search-result"></div>
        </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<div class="cds-dashboard-chat-main-container d-flex">
    @include('admin-panel.01-message-system.message-centre.chat_sidebar_header_common')

    <div class="chat-container" id="chat-container">
        @include('admin-panel.01-message-system.message-centre.chat_sidebar_common')
        <!-- Chat Messages -->
        <div class="bg-white message-container">
            <div class="new-connection-block">
                <div class="chat-messages pad20" id="chatMessages">
                    <div class="back-chats" onclick="backToConnectionList()">
                        <i class="fa-angle-left fa-solid"></i>
                        Back
                    </div>
                    <div class="row to-connet-div"></div>
                    <div id="common-skeleton-loader" style="display:none;">
                        @include('components.loaders.connect-loader')              
                    </div>
                </div>
            </div>
            <div class="bg-white text-center">
                <button class="d-none btn load-more-connection">Load More <i class="fa-loader fa-solid fa-spin fa-xl ms-2"></i></button>
            </div>
        </div>
    </div>
</div>
			</div>
	
	</div>
  </div>
</div>




@endsection
@section('javascript')
<script>
    // conversationList();
    let v_page = 1;
    toConnectList(v_page);
    pendingConversationList();
    connectConversationList('followers', this,'onload');
    function pendingConversationList() {
       
            $.ajax({
                type: 'post',
                url: "{{ baseUrl('connections/connect/pending-connected-list') }}",
                data: {
                    // type: type,
                    _token: "{{ csrf_token() }}" 
                },
                dataType: 'json',
                success: function (data) {
                  
                    $('#pending-connect-list').html(data.contents);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching messages:', error);
                }
            });
        // }
    }

    
    function sendConnection(user_id)
    {
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('connections/connect/send-connection') }}/"+user_id,
            dataType: 'json',
            success: function (response) {
                if(response.status == true){
                    successMessage(response.message);
                    pendingConversationList();
                    toConnectList(1);
                }else{
                    errorMessage(response.message);
                }
                // console.log(data.contents);
                // $('.to-connet-div').html(data.contents);
            },
            error: function (xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }

    function removeConnection(unique_id,type)
    {
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('connections/connect/remove-connection') }}/"+unique_id,
            dataType: 'json',
            success: function (response) {
                if(response.status == true){
                    successMessage(response.message);
                    connectConversationList(type, this,'onload');
                    pendingConversationList();
                    toConnectList(1);
                }else{
                    errorMessage(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }

    function followBack(user_id,type)
    {
        $.ajax({
            type: 'post',
            url: "{{ baseUrl('connections/connect/follow-back') }}",
            data: {
                user_id: user_id,
                _token: "{{ csrf_token() }}" 
            },
            dataType: 'json',
            success: function (data) {
                if(data.status == true){
                    successMessage(data.message);
                    connectConversationList(type, this,'onload');
                    toConnectList(1);
                }
               
            },
            error: function (xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }
    
    function unfollow(user_id,type)
    {
        
        Swal.fire({
        title: "Are you sure to unfollow?",
        text: "Your connection also removed",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        
        var remove_connection = "";
        if (result.value) {
            remove_connection = "yes";
        }else{
            remove_connection = "no";
        }
       
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('connections/connect/remove') }}/"+user_id+'/'+remove_connection,
            dataType: 'json',
            success: function (response) {
                if(response.status == true){
                    successMessage(response.message);
                    connectConversationList(type, this,'onload');
                    toConnectList(1);
                }else{
                    errorMessage(response.message);
                }
                // console.log(data.contents);
                // $('.to-connet-div').html(data.contents);
            },
            error: function (xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });

    });
    

        
    }

    function removeFromFollowers(user_id,type)
    {
        $.ajax({
            type: 'get',
            url: "{{ baseUrl('connections/connect/remove-from-followers') }}/"+user_id,
            dataType: 'json',
            success: function (response) {
                if(response.status == true){
                    successMessage(response.message);
                    connectConversationList(type, this,'onload');
                    toConnectList(1);
                }else{
                    errorMessage(response.message);
                }
              
            },
            error: function (xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }

    function connectConversationList(type,element,loadType) {
       
            if(loadType === 'click'){
                $('a').removeClass('active')
                $(element).addClass('active');
            }   
          
            $.ajax({
                type: 'post',
                url: "{{ baseUrl('connections/connect/connected-list') }}",
                data: {
                    type: type,
                    _token: "{{ csrf_token() }}" 
                },
                dataType: 'json',
                success: function (data) {
                    console.log(data.contents);
                    $('#connected-ist').html(data.contents);
                },
                error: function (xhr, status, error) {
                    console.log('Error fetching messages:', error);
                }
            });
        // }
    }



    function toConnectList(v_page) {
        var search = $("#search-users").val();
        $.ajax({
            type: 'get',
            url: BASEURL + '/connections/connect/connect-user-list?page=' + v_page,
            dataType: 'json',
            data:{
                search:search
            },
            beforeSend: function() {
                $("#common-skeleton-loader").show();
            },
            success: function (data) {

                if(data.current_page >= 3){
                    $('.load-more-connection').removeClass('d-none');
                    $('.load-more-connection .fa-spin').css('display','none');
                }
                if(v_page == 1){
                    $('.to-connet-div').html(data.contents);
                }else{
                    $('.to-connet-div').append(data.contents);
                }
                if(data.current_page == data.total_records) {
                    $(".load-more-connection").addClass('d-none');
                }
                $("#common-skeleton-loader").hide();
            },
            error: function (xhr, status, error) {
                console.log('Error fetching messages:', error);
            }
        });
    }

    
    $(window).scroll(function () {
       
       
        if ($(window).scrollTop() + $(window).height() >= $(".chat-messages").height()) {
           
                if(v_page < 3){
                    loading = true;
                    showLoader();
                    v_page++;
                    toConnectList(v_page);
                }
        
            
        }

  
    });

    $(document).on("click", ".load-more-connection", function () {
        v_page++;
        $('.load-more-connection .fa-spin').css('display','block');
          showLoader();
        toConnectList(v_page);
    });

    $(document).ready(function(){
        $("#search-users").keyup(function(){
            var value = $(this).val();
            if(value.length > 3){
                toConnectList(1);
            }else{
                if(value == ''){
                    toConnectList(1);
                }
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
    });
  
    function backToConnectionList() {
        console.log("123")
        const connectionList = document.querySelector(".new-connection-block")
        connectionList.classList.remove("active");
    }
</script>
@endsection