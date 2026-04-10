@foreach($records as $key => $record)
@php
    // Get current plan info
    $currentPlanId = $userSubscriptionHistory->membershipPlan->id ?? null;
    $currentPlanAmount = $userSubscriptionHistory->membershipPlan->amount ?? null;
@endphp

<div class="CDSSiteplans-Main-plan-card" style="animation-delay: {{ $key * 0.1 + 0.1 }}s">
    <div class="CDSSiteplans-Main-plan-header">
        <h3 class="CDSSiteplans-Main-plan-title">{{ $record->plan_title ?? '' }}</h3>
        <p class="CDSSiteplans-Main-plan-subtitle">{{ $record->description ?? '' }}</p>
    </div>
    
    <div class="CDSSiteplans-Main-price-container">
        <span class="CDSSiteplans-Main-plan-price">{{ currencySymbol($record->currency) }} {{ number_format($record->amount, 2) }}</span>
        <span class="CDSSiteplans-Main-plan-period">/month</span>
    </div>
    <ul class="CDSSiteplans-Main-features-list mb-3">
        @if($record->activeFeatures && $record->activeFeatures->count() > 0)
            @foreach($record->activeFeatures as $featureValue)
                @if($featureValue->feature)
                    <li>
                        <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"></path></svg>
                        {{ $featureValue->feature->feature_name }}
                        @if($featureValue->value && $featureValue->value > 0)
                            ({{ $featureValue->value }})
                        @elseif($featureValue->value == -1)
                            (Unlimited)
                        @endif
                    </li>
                @endif
            @endforeach
        @endif
    </ul>
    @if($record->id === $currentPlanId)
    <button class="CDSSiteplans-Main-plan-button" disabled>
        <span>Active Plan</span>
    </button>
    @elseif($currentPlanAmount !== null && isset($record->amount) && $record->amount < $currentPlanAmount)
    <p class="text-danger">You are not allowed to downgrade your plan.</p>
    @elseif(checkPrivilege([
        'route_prefix' => 'panel.membership-plans',
        'module' => 'professional-membership-plans',
        'action' => 'add'
    ]))
    <a href="{{ baseUrl('membership-plans/plans/' . $record->unique_id) }}" class="CDSSiteplans-Main-plan-button">
        <span>Choose Plan</span>
    </a>
    @endif
</div>
@endforeach

