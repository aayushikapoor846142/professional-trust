 <div class="CDSPostCaseDetail-modal" id="proposalModal">
        <div class="CDSPostCaseDetail-modal-content">
            <div class="CDSPostCaseDetail-modal-header">
                <h2 class="CDSPostCaseDetail-modal-title">Submit Your Proposal</h2>
                <div class="CDSPostCaseDetail-modal-header-actions">
                    <button class="CDSPostCaseDetail-btn-ai" onclick="generateProposal()">
                        <span>✨</span>
                        Generate Proposal Via AI
                    </button>
                    <button class="CDSPostCaseDetail-modal-close" onclick="closeProposalModal()">×</button>
                </div>
            </div>
            
            <form id="proposalForm" class="js-validate mt-3" action="{{ baseUrl('/cases/save-proposal') }}" method="POST"> 
                @csrf
                <!-- Sub Service Type -->
                <input type="hidden" name="case_id" value="{{$record->id }}">
                <div class="CDSPostCaseDetail-form-group">
                    @php
                        $options = [];
                        if(!empty($sub_services)){
                            $options = $sub_services->map(function($value) {
                                return [
                                    'id' => $value->id,
                                    'name' => ($value->subServiceTypes->name ?? '') . ' [$' . number_format($value->consultancy_fees ?? 0, 2) . ']',
                                ];
                            })->toArray();
                        }
                    @endphp
                    @if(!empty($sub_services))
                        {!! FormHelper::formSelect([
                            'name' => 'sub_service_type_id',
                            'required' => true,
                            'label' => 'Sub service type',
                            'options' => $options,
                            'value_column' => 'id',
                            'label_column' => 'name',
                            'is_multiple' => false,
                            'select_class' => 'CDSPostCaseDetail-form-select'
                        ]) !!}
                    @else
                        <div class="alert alert-danger m-0">Please add sub service types for this services. <a href="{{ baseUrl('/manage-services') }}">Click Here</a></div>
                    @endif
                    <!-- <select class="CDSPostCaseDetail-form-select" id="subServiceType" required>
                        <option value="docx-multiuser">Docx Multiuser (B100)</option>
                        <option value="visa-consultation">Visa Consultation</option>
                        <option value="document-review">Document Review</option>
                        <option value="full-service">Full Service Package</option>
                    </select> -->
                </div>

                <!-- Enter Description -->
                <div class="CDSPostCaseDetail-form-group">
                    {!! FormHelper::formTextarea(['name'=>"description",
                        'id'=>"description",
                        'required'=>true,
                        "label"=>"Enter Description",
                        'class'=>"cds-texteditor",
                        'textarea_class'=>"noval",
                        'value' => '',
                    ]) !!}
                    {{--<label class="CDSPostCaseDetail-form-label">
                        Enter Description <span class="CDSPostCaseDetail-required">*</span>
                    </label>
                    <div class="CDSPostCaseDetail-rich-editor">
                        <div class="CDSPostCaseDetail-editor-toolbar">
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="formatText('bold')" title="Bold">
                                <strong>B</strong>
                            </button>
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="formatText('italic')" title="Italic">
                                <em>I</em>
                            </button>
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="formatText('underline')" title="Underline">
                                <u>U</u>
                            </button>
                            <div class="CDSPostCaseDetail-editor-separator"></div>
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="formatText('insertUnorderedList')" title="Bullet List">
                                ☰
                            </button>
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="formatText('insertOrderedList')" title="Numbered List">
                                ≡
                            </button>
                            <div class="CDSPostCaseDetail-editor-separator"></div>
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="insertLink()" title="Insert Link">
                                🔗
                            </button>
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="undo()" title="Undo">
                                ↶
                            </button>
                            <button type="button" class="CDSPostCaseDetail-editor-btn" onclick="redo()" title="Redo">
                                ↷
                            </button>
                        </div>
                        <div 
                            class="CDSPostCaseDetail-editor-content" 
                            contenteditable="true" 
                            id="proposalDescription"
                            placeholder="Describe your approach and experience..."
                        ></div>
                    </div>--}}
                </div>

                <!-- Quotation Section -->
                <div class="CDSPostCaseDetail-quotation-section">
                    <h3 class="CDSPostCaseDetail-section-title">Quotation</h3>
                    @if($quotations->isNotEmpty())
                        <div class="CDSPostCaseDetail-form-group">
                            <label class="CDSPostCaseDetail-form-label">Select Quotation</label>
                            {!! FormHelper::formSelect([
                                'name' => 'quotation_id',
                                'label' => '',
                                'class' => 'select2-input ga-country',
                                'options' => $quotations,
                                'value_column' => 'id',
                                'label_column' => 'quotation_title',
                                "events" => ["onchange=fetchQuotation(this)"],
                                'is_multiple' => false,
                                'select_class' =>'CDSPostCaseDetail-form-select'
                            ]) !!}
                        </div>
                        <!-- Quotation Table -->
                        <div class="CDSPostCaseDetail-quotation-table-wrapper">
                            <table class="CDSPostCaseDetail-quotation-table">
                                <thead>
                                    <tr>
                                        <th>PARTICULAR</th>
                                        <th>AMOUNT</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="quotationItems">
                                    {{-- JavaScript will append dynamic rows here --}}
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <a href="javascript:void(0);"
                                                    class="CDSPostCaseDetail-add-item-btn"
                                                    id="add-item-btn">
                                                + Add Items
                                                </a>
                                        </td>
                                    </tr>
                                    <tr class="CDSPostCaseDetail-total-row">
                                        <td colspan="2" style="text-align: right; font-weight: 600;">Total:</td>
                                        <td colspan="2"  style="font-weight: 700; font-size: 18px;"><span  id="subtotal">0.00 </span></td>
                                    </tr>
                                </tfoot>
                            </table>

                            {{-- Hidden subtotal and total fields --}}
                            <div class="js-form-message">
                                <input type="hidden" name="sub_total" id="sub_total">
                            </div>
                            <div class="js-form-message">
                                <input type="hidden" name="total_amount" id="total_amount">
                            </div>

                            {{-- Error containers --}}
                            <div class="items_error_div text-danger"></div>
                            <div class="items_amount_error_div text-danger"></div>

                        </div>

                    @else
                        <div class="text-dark text-center">
                            No quotation is available for the service. Please add quotation. <a href="{{ baseUrl('/quotations/add') }}">Click Here</a>
                        </div>

                    @endif
                </div>

                <!-- Form Actions -->
                <div class="CDSPostCaseDetail-form-actions">
                    @if(!empty($sub_services) && $quotations->isNotEmpty())
                    <button type="submit" class="CDSPostCaseDetail-btn CDSPostCaseDetail-btn-primary CDSPostCaseDetail-btn-save">
                        Save
                    </button>
                    @else
                        <div class="alert-danger alert">You cannot send the proposal due to missing data</div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@push('scripts')
    <script>

@if (!empty($case_quotations->quotation_id))
        $(document).ready(function() {
            var selectedRadio = $('input[name="quotation_id"][value="{{ $case_quotations->quotation_id }}"]');
            if (selectedRadio.length) {
                fetchQuotation(selectedRadio[0]);
            }
        });
@endif

    $(document).ready(function() {
        initEditor("description");
        // invoiceDatePicker('invoice_date');
        // invoiceDatePicker('due_date');
        updateTotals();
        listProposalHistory();
        let subtotal = 0; 
        $("#proposalForm").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("proposalForm");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#proposalForm").attr('action');
            $('.invoice-error-message').remove();
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
                        
                        $.each(response.message, function (key, value) {

                            if(key != 'items' ){
                                let fieldName = key.replace(/\.(\d+)\./g, "[$1][").replace(/\.(\w+)/g, "[$1]");
                                // Ensure proper closing brackets
                                fieldName = fieldName.replace(/\]$/, '') + ']';

                                let inputField = $("[name='" + fieldName + "']");
                            
                                if (inputField.length > 0) {
                                    inputField.after(`<span class="invoice-error-message" style="color: red;">${value}</span>`);
                                }
                            }else{
                                errorMessage("Please add items");
                            }
                           
                        });

                        validation(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });

        });
        // Add new item row with validation
        $("#add-item-btn").click(function () {
            let emptyField = false;
            $('.invoice-error-message').remove();
            // Check if any existing item name field is empty
            $(".item-name").each(function () {
                if ($(this).val().trim() === "") {
                    emptyField = true;
                    return false; // Break loop
                }
            });

            if (emptyField) {
                errorMessage("Please fill in all item names before adding a new one.");
                return;
            }
          
            let rowCount = $("#quotationItems tr").length; // Get row count to make uniq
            let row = `<tr class="js-form-message">
                <td><input type="text" class="item-name form-control" name="items[${rowCount}][name]" placeholder="Enter item name"></td>
                <td><input type="number" name="items[${rowCount}][amount]" class="item-amount form-control" min="0" value="0"></td>
                <td class="CDSPostCaseDetail-total-cell"><input type="hidden" class="row-sub-total form-control" name="items[${rowCount}][row_sub_total]" disabled><span class="sub-total-span"></span></td>
                <td><button type="button" class="CDSPostCaseDetail-remove-btn invoice-remove-btn" onclick="removeQuotationItem(this)">
                                                X
                                            </button></td>
            </tr>`;
            $("#quotationItems").append(row);
        });

       
        $(document).on("blur", ".item-amount", function () {
            updateRowSubtotal($(this));
            updateTotals();
        });

        $(document).on("click", ".invoice-remove-btn", function () {
            $(this).closest("tr").remove();
            updateTotals();
        });

    });

    function updateRowSubtotal(element) {
        let row = element.closest("tr");
        let amount = parseFloat(element.val()) || 0;
        row.find(".row-sub-total").val(amount);
        row.find(".sub-total-span").html(amount);
    }

    function updateTotals() {
        let subtotal = 0;
        $(".row-sub-total").each(function () {
            subtotal += parseFloat($(this).val()) || 0;
        });
        let totalDiscount = parseFloat($("#total-discount-price").text()) || 0;
        let taxRate = parseFloat($(".invoice-tax").val()) || 0;
        let totalAfterDiscount = subtotal - totalDiscount;
        let taxAmount = (totalAfterDiscount * taxRate) / 100;
        let totalAmount = (totalAfterDiscount + taxAmount).toFixed(2);
        // $("#subtotal").text(subtotal);
          $("#subtotal").text('$'+subtotal);
        $("#sub_total").val(subtotal);
        $("#total-amount").text(totalAmount);
        $("#total_amount").val(totalAmount);
    }



   

    function fetchQuotation(e){
        if($(e).val() != ''){
            $.ajax({
                url: '{{ baseUrl('cases/fetch-quotation') }}',
                type: "post",
                data: {
                    _token:csrf_token,
                    quotation_id:$(e).val()
                },
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status == true) {
                        $("#quotationItems").html(response.contents);
                        setTimeout(() => {
                            updateTotals();
                        }, 800);
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });
        }else{
            $("#quotationItems").html('');
        }
        
    }

   
    function generateProposal() {
        $.ajax({
            url: "{{ baseUrl('cases/generate-case-proposal') }}",
            type: "post",
            data: {
                _token: csrf_token,
                services: "{{ $record->services->name ?? '' }} " + ' ' + "{{ $record->subServices->name ?? '' }}",
                case_id: "{{$record->id }}"
            },
            dataType: "json",
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {

                if (response.status == true) {
                    hideLoader();

                    if (editorInstance && editorInstance.editor && typeof editorInstance.editor.setContent === 'function') {
                        editorInstance.editor.setContent({
                            html: response.data
                        });
                        console.log('success.');
                        successMessage(response.message);
                        console.log(response.data);
                    } else {
                        console.error('Editor is not initialized or setContent method is unavailable.');
                    }
                    // $("#description").val(response.message);
                } else {
                    errorMessage(response.message);
                }
            },
            error: function () {
                internalError();
            }
        });
    }
    function listProposalHistory() {
		
    	$.ajax({
			type: "POST",
			url: BASEURL + '/cases/proposal-history',
			data: {
				_token: csrf_token,
                id: "{{$record->unique_id}}"
			},
      		dataType: 'json',
			beforeSend: function() {
				
				// $("#paginate").html('');
			},
      		success: function(data) {
				$(".notification-view-more-link").remove();
                last_page = data.last_page;
                if (data.contents.trim() === "") {
                        loading = true; // Prevent further requests
                        if (data.current_page === 1) {
                            $("#proposal-history").html('<div class="text-center text-danger">No Record Found</div>');
                        }else{
                            $("#proposal-history").html('');
                        }
                } else {
                   
                    if (data.current_page === 1) {
                        $(".notification-view-more-link").remove();
                       
                        if (data.contents.trim() === "") {
                            $("#proposal-history").html('<div class="text-center text-danger">No Record Found</div>');
                        }else{
                            $("#proposal-history").html(data.contents);
                        }
                    } else {
                        $("#proposal-history").append(data.contents);
                    }
                    var next_page = data.current_page + 1;
                    if (data.last_page >= next_page) {
                        loading = false; // Allow further requests
                    } else {
                        loading = true; // Prevent further requests
                    }
                    if(data.last_page == 0){
                        $(".no-record-available").removeClass('d-none');
                        $(".load-more").html('No more data to load...');
                    }
                  
                }
                
      		},
    	});
  	}
</script>
@endpush