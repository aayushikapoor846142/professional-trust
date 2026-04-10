@extends('admin-panel.layouts.app')
@section('content')


<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <h3>Group: {{$getGroup->name}}</h3>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
           
            @php
            $unique_id= $record?$record->unique_id:randomNumber();
            @endphp
            <form id="form" class="js-validate" action="{{ baseUrl('/group-settings/update/'.$unique_id) }}" method="post">
                @csrf
                <div class="row">
                       <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="only_admins_can_post" value="1" class="form-check-input"
                                 {{ isset($record) && $record->only_admins_can_post=="1" ? 'checked' : '' }}>
                                Only admins can post
                            </label>
                      </div>
                       <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="members_can_add_members" value="1" class="form-check-input"
                                 {{ isset($record) && $record->members_can_add_members=="1" ? 'checked' : '' }}>
                                Members can add other members
                            </label>
                        </div>
                        <input type="hidden" value="{{$groupId}}" name="group_id">
                        <div class="mb-3">
                            <label class="form-label d-block mb-2">Who can see my message?</label>
                            <input class="form-check-input" type="radio" name="who_can_see_my_message" value="everyone" onclick="toggleMemberList()"
                                {{ isset($record) && $record->who_can_see_my_message == 'everyone' ? 'checked' : '' }}>
                            <label class="form-check-label">Everyone</label>

                            <input class="form-check-input" type="radio" name="who_can_see_my_message" value="admins" onclick="toggleMemberList()" 
                                {{ isset($record) && $record->who_can_see_my_message == 'admins' ? 'checked' : '' }}>
                            <label class="form-check-label">Only Admins</label>

                            <input class="form-check-input" type="radio" name="who_can_see_my_message" value="members" onclick="toggleMemberList()"
                                {{ isset($record) && $record->who_can_see_my_message == 'members' ? 'checked' : '' }}>
                            <label class="form-check-label">Only Members</label>
                            <div id="memberList" class="mt-2 {{ isset($record) && $record->who_can_see_my_message != 'members' ? 'hidden' : '' }}">
                                <label class="form-label">Select Members:</label>
                                    <ul>
                                       @foreach($members as $member)
                                            <li>
                                                <label>
                                                    <input type="checkbox" name="visible_members[]" value="{{ $member->id }}"
                                                        {{ isset($selectedMembers) && in_array($member->id, $selectedMembers) ? 'checked' : '' }}>
                                                    {{ $member->first_name . ' ' . $member->last_name }}
                                                </label>
                                            </li>
                                        @endforeach


                                    </ul>
                            </div>

                        </div>                    
                </div>
                  <div class="text-start mt-3">
                <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
            </div>
            </form>
			</div>
	
	</div>
  </div>
</div>
<div class="container-fluid">
  
    
    <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-header">
        </div>
        <div class="cds-ty-dashboard-box-body cds-form-container">

          
        </div>
    </div>
</div>

@endsection
<!-- End Content -->
@section('javascript')
<script>
     function toggleMemberList() {
            const selected = document.querySelector('input[name="who_can_see_my_message"]:checked').value;
            const memberListDiv = document.getElementById('memberList');
            memberListDiv.style.display = (selected === 'members') ? 'block' : 'none';
        }
    $(document).ready(function() {
       

        // Show/hide on page load
        document.addEventListener('DOMContentLoaded', toggleMemberList);

        e.preventDefault();
        $("#form").submit(function(e) {
            
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');
            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        redirect(response.redirect_back);
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });

        });
    });
</script>
@endsection