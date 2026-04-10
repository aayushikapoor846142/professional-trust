
 <div class="cdsTYDashboard-amount-contributed-container-body">
    <div class="cds-t25n-content-professional-profile-container-main-navigation">
        <ul class="onetime-tabs">
            <li class="cds-active">
                <a href="javascript:;" class="onetime-tab-link cds-active" data-tab="overview">Overview</a>
            </li>
            <li>
                <a href="javascript:;" class="onetime-tab-link" data-tab="basic-details">Basic Details</a>
            </li>
            <li>
                <a href="" class="onetime-tab-link" data-tab="address-details">Address Details</a>
            </li>
        </ul>
    </div>
    <div id="overview" class="onetime-tab-content">
        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment  cdsTYDashboard-amount-contributed-container-body-list-segment-highlight">
            <h5>Payment Type: <span>{{ $record->payment_type ?? '' }}<span></h5>
            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-body">
                <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-row"> 	
                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                        <span>SubTotal :</span>
                        $ {{ $record->amount ?? '' }}
                    </div>
                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                        <span>Tax :</span>
                        {{ $record->tax ?? '' }}%
                    </div>
                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                        <span>Total :</span>
                        ${{ $record->total_amount ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="basic-details" class="onetime-tab-content d-none">
        @if ($record->subscription_data)
            @php
                $subscription = json_decode($record->subscription_data, true);
            @endphp
            @if ($subscription)
                <div class="cdsTYDashboard-amount-contributed-container-body-list-segment">
                    <h5 >Basic Details</h5>
                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-body"> 
                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-row"> 	
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells"> 
                                <span>	First Name:</span>
                                {{ 	$subscription['first_name'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells"> 	 
                                <span>	Last Name:</span>
                                {{ $subscription['last_name'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">  
                                <span>Email:</span>
                                {{ $subscription['email'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">   
                                <span>Phone Number:</span>
                                {{ $subscription['phone_no'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                
            @endif
        @endif
    </div>
    <div id="address-details" class="onetime-tab-content d-none">
            @if ($record->subscription_data)
                @php
                    $subscription = json_decode($record->subscription_data, true);
                @endphp
                @if ($subscription)
                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment">
                    <h5 >Address Details</h5>
                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-body"> 
                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-row"> 	
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells"> 
                                <span>	Address:</span>
                                {{ $subscription['address'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells"> 
                                <span >City:</span>
                                {{ $subscription['city'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells"> 
                                <span >State:</span>
                                {{ $subscription['state'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">  
                                <span >Zip Code:</span>
                                {{ $subscription['zip'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">  
                                <span >Country: </span >
                                {{ $subscription['country'] ?? 'N/A' }}
                            </div>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells"> 
                                <span >Zip Code:</span>
                                {{ $subscription['zip'] ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                        <div class="cdsTYDashboard-amount-contributed-container-body-list-segment">
                            <h5 >Billing Address</h5>
                            <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-body"> 
                                <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-row"> 	
                                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">   
                                        <span > Billing Address:</span>
                                        {{ $subscription['b_address'] ?? 'N/A' }}
                                    </div> 
                                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells"> 
                                        <span > Billing City:</span>
                                        {{ $subscription['b_city'] ?? 'N/A' }}
                                    </div>
                                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                        <span>Billing State:</span>
                                        {{ $subscription['b_state'] ?? 'N/A' }}
                                    </div>
                                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">  
                                        <span >Country: </span >
                                        {{ $subscription['country'] ?? 'N/A' }}
                                    </div>
                                    <div class="cdsTYDashboard-amount-contributed-container-body-list-segment-cells">
                                        <span > Billing Zip Code:</span>
                                        {{ $subscription['b_zip'] ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>	
                @endif
            @endif
    </div>
 </div>

       <script>
          $(document).ready(function() {
            $('.onetime-tab-link').on('click', function(e) {
                e.preventDefault();
            
                $('.onetime-tabs li').removeClass('cds-active');

                $('.onetime-tab-content').addClass('d-none');

                $(this).parent('li').addClass('cds-active');

                const targetId = $(this).data('tab');
                $('#' + targetId).removeClass('d-none');
            });

            $('.closeOneTimeSlideBtn').on('click', function () {
                $('.OneTimeSlideView').removeClass('active');
            });
        });
       </script>

