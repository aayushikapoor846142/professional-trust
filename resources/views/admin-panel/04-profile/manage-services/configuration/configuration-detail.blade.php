@foreach($records as $record)
    <div class="CDSDashboardProfessionalServices02-type-config-item">
        <div class="CDSDashboardProfessionalServices02-type-config-details">
            <div class="CDSDashboardProfessionalServices02-type-name">
                @php
                    $typeNames = [];
                    if($record->subServicesType) {
                        $typeNames[] = $record->subServicesType->name;
                    }
                @endphp
                @foreach($typeNames as $typeName)
                    <span class="CDSDashboardProfessionalServices02-type-icon">👤</span>
                @endforeach
                {{ implode(' • ', $typeNames) ?: 'Applicant Type' }}
            </div>
            <div class="CDSDashboardProfessionalServices02-config-info">
                <div class="CDSDashboardProfessionalServices02-config-info-item">
                    <label>Professional Fee</label>
                    <span>${{ number_format($record->professional_fees, 2) }}</span>
                </div>
                <div class="CDSDashboardProfessionalServices02-config-info-item">
                    <label>Consultant Fee</label>
                    <span>${{ number_format($record->consultancy_fees, 2) }}</span>
                </div>
                <div class="CDSDashboardProfessionalServices02-config-info-item">
                    <label>Total Fee</label>
                    <span style="color: #5b4be7; font-weight: 600;">
                        ${{ number_format($record->professional_fees + $record->consultancy_fees, 2) }}
                    </span>
                </div>
                <div class="CDSDashboardProfessionalServices02-config-info-item">
                    <label>Assessment Form</label>
                    <span>{{ $record->form ? $record->form->name : 'Not selected' }}</span>
                </div>
            </div>
            
            @if($record->document_folders)
                <div class="CDSDashboardProfessionalServices02-config-info-item" style="margin-top: 8px;">
                    <label>Required Documents</label>
                    <div class="CDSDashboardProfessionalServices02-documents-list">
                        @php
                            $documentIds = explode(',', $record->document_folders);
                            $documents = \App\Models\DocumentsFolder::whereIn('id', $documentIds)->get();
                        @endphp
                        @foreach($documents as $document)
                            <span class="CDSDashboardProfessionalServices02-doc-badge">{{ $document->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif
            
        </div>
        <div class="CDSDashboardProfessionalServices02-service-actions">
            <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-outline" onclick="editConfiguration('{{ $record->unique_id }}')">
                Edit
            </button>
            <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-danger" onclick="removeConfiguration('{{ $record->unique_id }}')">
                Remove
            </button>
        </div>
    </div>
@endforeach