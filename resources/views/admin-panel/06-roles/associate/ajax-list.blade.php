@foreach($records as $key => $record)
 <div class="CDSProfessionalList-view-list-card">
    <div class="CDSProfessionalList-view-card-content">
        <div class="CDSProfessionalList-view-avatar-wrapper">
            <!-- <div class="CDSProfessionalList-view-avatar CDSProfessionalList-view-gradient-1">NP</div> -->
            {!! getProfileImage($record->unique_id) !!}
            <div class="CDSProfessionalList-view-status-dot"></div>
        </div>
        
        <div class="CDSProfessionalList-view-info-wrapper">
            <div class="CDSProfessionalList-view-name-row">
                <h3 class="CDSProfessionalList-view-name">{{$record->first_name ?? ''}} {{$record->last_name ?? ''}}</h3>
            </div>
            <p class="CDSProfessionalList-view-title">{{$record->email}}</p>
            <div class="CDSProfessionalList-view-details-row">
              
               
            </div>
        </div>
        
        <div class="CDSProfessionalList-view-stats-section">
            
            <div class="CDSProfessionalList-view-actions">
                @if(!empty($record->professionalJoiningRequest))
                   

                    @if($record->professionalJoiningRequest->status == 0)

                        <a class="CDSProfessionalList-view-btn CDSProfessionalList-view-btn-primary" onclick="openCustomPopup(this)" data-href="{{ baseUrl('associates/view-join-request/'.$record->professionalJoiningRequest->unique_id) }}" href="javascript:;">
                            View Joining Request
                        </a>
                        <a class="CDSProfessionalList-view-btn CDSProfessionalList-view-btn-primary" onclick="confirmAccept(this)" data-href="{{baseUrl('associates/accept-proposal/'.$record->unique_id)}}"  href="javascript:;">
                            Accept
                        </a>
                        <a class="CDSProfessionalList-view-btn CDSProfessionalList-view-btn-danger" onclick="confirmReject(this)" data-href="{{baseUrl('associates/reject-proposal/'.$record->unique_id)}}" href="javascript:;">
                            Reject
                        </a>

                    @endif
                    @if($record->professionalJoiningRequest->status == 1)
                        @if(empty($record->associateAgreement))
                            <a href="{{ baseUrl('agreement/' . $record->unique_id) }}" class="CDSProfessionalList-view-btn CDSProfessionalList-view-btn-primary">Create Agreement</a>
                        @endif
                    @endif

                    @if(!empty($record->associateAgreement))
                        <a href="{{ baseUrl('agreement/view/' . $record->associateAgreement->unique_id) }}" class="CDSProfessionalList-view-btn CDSProfessionalList-view-btn-primary">View Agreement</a>
                    @endif
                @endif
                
                <a href="{{ mainTrustvisoryUrl() . '/auth/professionals/' . $record->unique_id . '/' . str_slug($record->first_name . '-' . $record->last_name) }}" class="CDSProfessionalList-view-btn CDSProfessionalList-view-btn-secondary">View Profile</a>

                
            </div>
        </div>
    </div>
</div>
@endforeach

 @if(!empty($records) && $current_page > 2 && $current_page < $last_page)
<div class="professional-view-more-link text-center mt-4">
    <a href="javascript:;" onclick="loadData({{ $next_page }})" class="btn btn-primary">
        View More <i class="fa fa-chevron-down"></i>
    </a>
</div>
@endif
<script type="text/javascript">
$(document).ready(function() {
    $(".row-checkbox").change(function() {
        if ($(".row-checkbox:checked").length > 0) {
            $("#datatableCounterInfo").show();
        } else {
            $("#datatableCounterInfo").show();
        }
        $("#datatableCounter").html($(".row-checkbox:checked").length);
    });
})
</script>