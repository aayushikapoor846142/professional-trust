@if (!empty($records) && $records->isNotEmpty())
    @foreach ($records->where('status', 'sent') as $case)
        <div class="CDSProposals-card mt-5">

            <!-- Professional Header -->
            <div class="CDSProposals-professional-header">
                <div class="CDSProposals-professional-info">
                    <!-- <div class="CDSProposals-professional-avatar no-image">
                        
                    </div> -->
                    {!! getProfileImage($case->addedBy->unique_id) !!}
                    <div class="CDSProposals-professional-details">
                        <h3>{{ $case->addedBy->first_name ?? ''}}  {{ $case->addedBy->last_name ?? ''}}</h3>
                        <div class="CDSProposals-professional-company">{{ $case->addedBy->cdsCompanyDetail->company_name ?? '—' }}</div>
                        <div class="CDSProposals-professional-location">
                            <span>📍</span>
                            <span>{{ $case->addedBy->personalLocation->city ?? '' }}, {{ $case->addedBy->personalLocation->country ?? '' }}</span>
                        </div>
                        <div class="CDSProposals-professional-tags">
                            <span class="CDSProposals-tag">
                                {{ $case->case->client->experience_level ?? 'New Professional' }}
                            </span>
                            <span class="CDSProposals-tag">
                                {{ $case->case->client->experience_years ?? 0 }} Years Experience
                            </span>
                        </div>
                    </div>
                </div>
                <div class="CDSProposals-submission-time">
                    <span style="font-size: 13px; color: #999;">Submitted {{getTimeAgo($case->created_at ?? '' )}}</span>
                </div>
            </div>

            <!-- Proposal Content -->
            <div class="CDSProposals-content">
                <div class="CDSProposals-description">
                    {!! html_entity_decode($case->comments ?? '<em>No proposal message.</em>') !!}
                </div>

                <!-- Quotation Section -->
                @php $quotation = optional(optional($case->caseProposalHistory)->caseQuotation); @endphp
                @if($quotation && $quotation->particulars && $quotation->particulars->isNotEmpty())
                    <div class="CDSProposals-quotation">
                        <h4 class="CDSProposals-quotation-title">Quotation Details</h4>
                        <table class="CDSProposals-quotation-table">
                            <thead>
                                <tr>
                                    <th>PARTICULAR</th>
                                    <th style="text-align: right;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotation->particulars as $item)
                                    <tr>
                                        <td>{{ $item->particular }}</td>
                                        <td class="amount">${{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="CDSProposals-quotation-total">
                                    <td><strong>Total:</strong></td>
                                    <td class="amount"><strong>${{ number_format($quotation->total_amount, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                <!-- Service Details -->
                <div class="CDSProposals-service-details">
                    <div class="CDSProposals-detail-item">
                        <span class="CDSProposals-detail-label">Service Offer</span>
                        <span class="CDSProposals-detail-value">
                            {{ $case->professionalSubservice->subServiceTypes->name ?? '' }}
                        </span>
                    </div>
                    <div class="CDSProposals-detail-item">
                        <span class="CDSProposals-detail-label">Initial Consultancy Fees</span>
                        <span class="CDSProposals-detail-value">
                            ${{ number_format($case->professionalSubservice->consultancy_fees ?? 0, 2) }}
                        </span>
                    </div>
                    <div class="CDSProposals-detail-item">
                        <span class="CDSProposals-detail-label">Status</span>
                        <span class="CDSProposals-detail-value">{{ ucfirst($case->status ?? '—') }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="CDSProposals-actions">
                @if($case->status === 'sent')
                    @if($case_history && $case_history->status == 'awarded')
                        <button class="CDSProposals-btn CDSProposals-btn-view">Awarded Case</button>
                        <a href="{{ baseUrl('/case-with-professionals/retain-agreements/'.$case_history->casewithProfessional->unique_id) }}"
                           class="CDSProposals-btn CDSProposals-btn-award">Go to Retain Agreements</a>
                    @else
                        <a href="javascript:;" onclick="editProposal(this)" data-href="{{ baseUrl('/cases/edit-proposal/'.$case->case->unique_id) }}"
                           class="CDSProposals-btn CDSProposals-btn-view">Edit</a>
                    @endif
                @endif

            </div>
        </div>
    @endforeach

    @if($records->where('status', 'withdraw')->count() != 0)
        <button class="toggleBtn CDSPostCaseDetail-btn CDSPostCaseDetail-btn-primary mt-5 mb-2">Revise Proposal ({{$records->where('status', 'withdraw')->count()}})</button>
        @foreach ($records->where('status', 'withdraw') as $case)
            <div class="CDSProposals-card mt-5 CDSProposals-withdraw" style="display: none;">

                <!-- Professional Header -->
                <div class="CDSProposals-professional-header">
                    <div class="CDSProposals-professional-info">
                        <!-- <div class="CDSProposals-professional-avatar no-image">
                            
                        </div> -->
                        {!! getProfileImage($case->addedBy->unique_id) !!}
                        <div class="CDSProposals-professional-details">
                            <h3>{{ $case->addedBy->first_name ?? ''}}  {{ $case->addedBy->last_name ?? ''}}</h3>
                            <div class="CDSProposals-professional-company">{{ $case->addedBy->cdsCompanyDetail->company_name ?? '—' }}</div>
                            <div class="CDSProposals-professional-location">
                                <span>📍</span>
                                <span>{{ $case->addedBy->personalLocation->city ?? '' }}, {{ $case->addedBy->personalLocation->country ?? '' }}</span>
                            </div>
                            <div class="CDSProposals-professional-tags">
                                <span class="CDSProposals-tag">
                                    {{ $case->case->client->experience_level ?? 'New Professional' }}
                                </span>
                                <span class="CDSProposals-tag">
                                    {{ $case->case->client->experience_years ?? 0 }} Years Experience
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="CDSProposals-submission-time">
                        <span style="font-size: 13px; color: #999;">Submitted {{getTimeAgo($case->created_at ?? '' )}}</span>
                    </div>
                </div>

                <!-- Proposal Content -->
                <div class="CDSProposals-content">
                    <div class="CDSProposals-description">
                        {!! html_entity_decode($case->comments ?? '<em>No proposal message.</em>') !!}
                    </div>

                    <!-- Quotation Section -->
                    @php $quotation = optional(optional($case->caseProposalHistory)->caseQuotation); @endphp
                    @if($quotation && $quotation->particulars && $quotation->particulars->isNotEmpty())
                        <div class="CDSProposals-quotation">
                            <h4 class="CDSProposals-quotation-title">Quotation Details</h4>
                            <table class="CDSProposals-quotation-table">
                                <thead>
                                    <tr>
                                        <th>PARTICULAR</th>
                                        <th style="text-align: right;">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quotation->particulars as $item)
                                        <tr>
                                            <td>{{ $item->particular }}</td>
                                            <td class="amount">${{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="CDSProposals-quotation-total">
                                        <td><strong>Total:</strong></td>
                                        <td class="amount"><strong>${{ number_format($quotation->total_amount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Service Details -->
                    <div class="CDSProposals-service-details">
                        <div class="CDSProposals-detail-item">
                            <span class="CDSProposals-detail-label">Service Offer</span>
                            <span class="CDSProposals-detail-value">
                                {{ $case->professionalSubservice->subServiceTypes->name ?? '' }}
                            </span>
                        </div>
                        <div class="CDSProposals-detail-item">
                            <span class="CDSProposals-detail-label">Initial Consultancy Fees</span>
                            <span class="CDSProposals-detail-value">
                                ${{ number_format($case->professionalSubservice->consultancy_fees ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="CDSProposals-detail-item">
                            <span class="CDSProposals-detail-label">Status</span>
                            <span class="CDSProposals-detail-value">{{ ucfirst($case->status ?? '—') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="CDSProposals-actions">
                    @if($case->status === 'sent')
                        @if($case_history && $case_history->status == 'awarded')
                            <button class="CDSProposals-btn CDSProposals-btn-view">Awarded Case</button>
                            <a href="{{ baseUrl('/case-with-professionals/retain-agreements/'.$case_history->casewithProfessional->unique_id) }}"
                            class="CDSProposals-btn CDSProposals-btn-award">Go to Retain Agreements</a>
                        @else
                            <a href="javascript:;" onclick="editProposal(this)" data-href="{{ baseUrl('/cases/edit-proposal/'.$case->case->unique_id) }}"
                            class="CDSProposals-btn CDSProposals-btn-view">Edit</a>
                        @endif
                    @endif

                    <button class="CDSProposals-btn CDSProposals-btn-message">Message</button>
                    <button class="CDSProposals-btn CDSProposals-btn-award">Award Contract</button>
                </div>
            </div>
        @endforeach
    @endif
@else
    <p>No Proposal.</p>
@endif

<script>

    $('.toggleBtn').on('click', function () {
        $('.CDSProposals-withdraw').toggle(); // Toggle all elements with this class
    });
    function editProposal(e)
    {
        var url = $(e).attr("data-href");
        $.ajax({
            type: "GET",
            url: url,
            data: {
                _token: csrf_token,
            },
            dataType: "json",
            success: function (response) {
                $("#editProposalModal").html(response.contents);
                // Show modal
                document.getElementById('editProposalModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            },
            error: function () {
                internalError();
            },
        });
    }
    function closeEditProposalModal() {
        document.getElementById('editProposalModal').classList.remove('active');
        document.body.style.overflow = '';
        document.getElementById('editProposalModal').reset();
    }
    //   initEditor("edit-description");
</script>
