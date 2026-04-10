@foreach($records as $index => $value)
    @php
        // Check if this service has existing configurations
        $existingConfigurations = \App\Models\ProfessionalSubServices::where('professional_service_id', $value->id)
            ->where('user_id', auth()->user()->id)
            ->with(['subServicesType', 'form'])
            ->get();
        $hasConfigurations = $existingConfigurations->count() > 0;
    @endphp
    
    <div class="CDSDashboardProfessionalServices02-service-item" data-service-id="{{ $value->unique_id }}" data-parent-service-id="{{ $value->parent_service_id }}">
        <div class="CDSDashboardProfessionalServices02-service-header">
            <div class="CDSDashboardProfessionalServices02-service-details">
                <h5>{{$value->subServices->name}}</h5>
                <div class="CDSDashboardProfessionalServices02-service-meta">
                    @if($hasConfigurations)
                        <div class="CDSDashboardProfessionalServices02-meta-item">
                            <span class="CDSDashboardProfessionalServices02-configured-badge">Configured</span>
                        </div>
                        <div class="CDSDashboardProfessionalServices02-meta-item">
                            {{ $existingConfigurations->count() }} configuration{{ $existingConfigurations->count() > 1 ? 's' : '' }}
                        </div>
                    @else
                        <div class="CDSDashboardProfessionalServices02-meta-item" style="color: #ef4444;">
                            Not configured
                        </div>
                    @endif
                </div>
            </div>
            <div class="CDSDashboardProfessionalServices02-service-actions">
               @if($existingConfigurations->count() < $subServiceType->count())
                    <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-outline"
                        onclick="configureService(this)"
                        data-unique-id="{{ $value->unique_id }}"
                        data-service-name="{{ $value->subServices->name }}">
                        Add Configuration 
                    </button>
                @endif

                @if($value->checkIfCaseLinked(auth()->user()->getRelatedProfessionalId(),$value->parent_service_id,$value->service_id) == 0)
                    <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-danger" onclick="removeServiceFromServer('{{ $value->unique_id }}')">
                        Remove
                    </button>
                @else
                    <div class="text-primary">Case Connected</div>
                @endif
               
            </div>
        </div>
        <div class="CDSDashboardProfessionalServices02-type-configurations">
            @if($hasConfigurations)
                @include('admin-panel.04-profile.manage-services.configuration.configuration-detail', ['records' => $existingConfigurations])
            @endif
        </div>
    </div>
@endforeach