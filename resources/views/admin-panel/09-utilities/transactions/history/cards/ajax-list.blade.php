@foreach($records as $key => $record)
<div class="cdsTYDashboard-manage-cards-list-segment">
    <div class="cdsTYDashboard-manage-cards-list-segment-header">
        {{$record->billing_details->name}}
    </div>
  
    {{ $record->card->display_brand ?? '' }}
    <div class="cdsTYDashboard-manage-cards-list-segment-body">
        XXXX-XXXX-XXXX-{{$record->card->last4}}
    </div>
    <div class="cdsTYDashboard-manage-cards-list-segment-footer">
        <div class="cdsTYDashboard-manage-cards-list-segment-footer-left">
            {{ ($defaultPaymentMethod->id == $record->id) ? 'Yes' : 'No' }}
        </div>
        <div class="cdsTYDashboard-manage-cards-list-segment-footer-right">
            <div class="btn-group">
                <a class="dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                    <i class="fa-solid fa-circle-ellipsis-vertical"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                    <li>
                        <a class="dropdown-item text-danger font14" href="javascript:;" onclick="confirmPaymentMethod(this)" data-href="{{ baseUrl('payment-methods/default/'.$record->id) }}">Make Default </a>
                    </li>
                    <li>
                        <a
                            class="dropdown-item text-danger font14"
                            href="javascript:;"
                            onclick="removePaymentMethod(this)"
                            data-default="{{ $defaultPaymentMethod->id == $record->id ? 'Yes' : 'No' }}"
                            data-href="{{ baseUrl('payment-methods/delete/'.$record->id) }}"
                        >
							Remove
                        </a>
                    </li>
                </ul>
       

            </div>
        </div>
    </div>
</div>
@endforeach