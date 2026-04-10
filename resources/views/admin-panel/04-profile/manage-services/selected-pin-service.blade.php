@if($services->isNotEmpty())
    @foreach ($services as $key => $sub_service)
        <div class="CDSDashboardProfessionalServices-list-service-card">
            <div class="CDSDashboardProfessionalServices-list-service-header">
                <div>
                    <h3 class="CDSDashboardProfessionalServices-list-service-title">{{$sub_service->parentService->name}} -> {{$sub_service->subServices->name}} </h3>
                </div>
                <button onclick="markAsPin('{{ $sub_service->id }}',0)" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-text">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M10 1v6h6M4 7V1h6l6 6v8a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-5M1 10h5v5"></path>
                    </svg>
                    <span class="CDSDashboardProfessionalServices-list-hide-mobile">Unpin</span>
                </button>
            </div>
        </div>
    @endforeach
@else
    <div class="CDSDashboardProfessionalServices-list-service-card">
        <span class="alert alert-warning">No service pinned yet</span>
    </div>
@endif