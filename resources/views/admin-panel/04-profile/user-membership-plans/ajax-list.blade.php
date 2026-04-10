<div class="cds-ty-dashboard-box">

    <div class="cds-ty-dashboard-box-body">
        <div class="cds-ty-dashboard-segments">
            @if($record && $record->membershipPlan)
            @if($record->subscription_status === 'cancelled')
            <p class="plan-title mb-0"><i class="fa-solid fa-box icon"></i> No purchased plan yet.</p>
            @else
            <div class="membership-heading">
                <div>
                    <span>Subscriptions</span>
                    <h3 class="plan-title">{{$record->membershipPlan->plan_title ?? ''}}</h3>
                </div>
                <div>
                    <p class="price">Price: ${{$record->membershipPlan->amount ?? ''}}<span>/month</span></p>
                </div>
            </div>
            <div class="membership-details">
                <h4>Subscriptions Details</h4>
                <div class="membership-details-block">
                    <div class="cds-membership-details-block-segments">
                        <h6>Customer</h6>
                        <p>{{$record->user->first_name ?? ''}} {{$record->user->last_name ?? ''}}</p>
                    </div>
                    <div class="cds-membership-details-block-segments">
                        <h6>Status</h6>
                        <p class="membership-status">{{$record->subscription_status ?? ''}}</p>
                    </div>

                    <div class="cds-membership-details-block-segments">
                        <h6>Current Period</h6>
                     
                        <p>{{ \Carbon\Carbon::createFromTimestamp($currentSubscription->current_period_start ?? 0)->format('d-M') .' to '.  \Carbon\Carbon::createFromTimestamp($currentSubscription->current_period_end ?? 0)->format('d-M-Y') }}
                        </p>
                    </div>
                    <div class="cds-membership-details-block-segments">
                        <h6>Created</h6>
                        <p>{{$record->created_at ? $record->created_at : ''}}</p>
                    </div>
                    <div class="cds-membership-details-block-segments">
                        <h6>Payment Method</h6>
                        <p>
                            {{ $record->user->pm_type ?? 'N/A' }} ....  
                            {{ isset($record->user->pm_last_four) ? decryptVal($record->user->pm_last_four) : 'N/A' }}

                        </p>
                    </div>
                    <div class="cds-membership-details-block-segments">
                        <a class="cds-membership-manage-card-button"
                            href="{{ baseUrl('my-membership-plans/add-card/'.$record->unique_id) }}">
                            <i class="fa-light fa-credit-card fa-xl"></i> Manage Cards
                        </a>

                    </div>
                </div>
                <div class="membership-details-cancel-block">
                    <a data-href="{{ baseUrl('my-membership-plans/cancel/'.$record->stripe_subscription_id) }}"
                        onclick="confirmAnyAction(this)" title="Cancel Subscription"
                        data-action="Cancel Subscription">
                        Cancel
                        Subscription
                    </a>
                   
                </div>
            </div>
            @endif
            @else
            <p class="plan-title mb-0"><i class="fa-solid fa-box icon"></i> No purchased plan yet.</p>
            @endif
            <!-- <div class="cds-my-membership">
            </div> -->
        </div>
      

    </div>
</div>